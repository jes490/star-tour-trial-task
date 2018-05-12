<?php

namespace App\Services;

use App\Exceptions\HTTPResolverException;
use App\Models\TaskRequest;
use App\Models\TaskConstraint;
use App\Models\TaskError;
use App\Models\TaskResult;
use App\Http\Resources\TaskRequestCollection;

/**
 * Task Parser.
 *
 * Class Parser
 * @author Alexey Sesyolkin
 * @email aseselkin@gmail.com
 */
class Parser implements ParserInterface
{
    /**
     * Input Parser.
     * @var InputParser
     */
    protected $iParser;

    /**
     * Output Parser.
     * @var OutputParser
     */
    protected $oParser;

    /**
     * URL Resolver.
     * @var HTTPResolver
     */
    protected $urlResolver;

    /**
     * Parser constructor.
     * @param InputParser $iParser
     * @param OutputParser $oParser
     * @param HTTPResolver $urlResolver
     */
    public function __construct(InputParser $iParser, OutputParser $oParser, HTTPResolver $urlResolver)
    {
        $this->iParser = $iParser;
        $this->oParser = $oParser;
        $this->urlResolver = $urlResolver;
    }

    /**
     * Formats input data, loads and parses external content based on that data.
     *
     * @param string $text
     * @return TaskRequestCollection
     */
    public function parse($text)
    {
        $formattedData = $this->iParser->parse($text);

        foreach ($formattedData as $index => $request)
        {
            $formattedData[$index]['results'] = [];
            $formattedData[$index]['errors'] = [];

            try {
                $remoteContent = $this->urlResolver->getByCurl($request['url']);
                $results = $this->oParser->parse($remoteContent, $request['constraints']);
                
                if (!$results) {
                    $remoteContent = $this->urlResolver->getByPhantomJs($request['url']);
                    $results = $this->oParser->parse($remoteContent, $request['constraints']);
                }
            } catch (HTTPResolverException $e) {
                $formattedData[$index]['status'] = 'Error';
                $formattedData[$index]['errors'] = $this->getHTTPExceptionErrors($e);
                continue;
            } catch (\Exception $e) {
                $formattedData[$index]['status'] = 'Error';
                $formattedData[$index]['errors'] = [['message' => $e->getMessage()]];
                continue;
            }
            foreach ($results as $i => $result)
                $results[$i] = $this->format($result);
            $formattedData[$index]['status'] = 'Success';
            $formattedData[$index]['results'] = $results;
        }
        
        return new TaskRequestCollection($this->save($formattedData));
    }

    /**
     * Format external text.
     *
     * @param $text
     * @return null|string|string[]
     */
    protected function format($text)
    {
        $text = strip_tags($text, "<p><b><strong><span><h1><h2><h3><h4><h5><h6><em><i>
            <table><thead><tbody><tfoot><tr><td><th><bdo><basefont><abbr><acronym><cite><code>
            <dfn><q><s><samp><small><strike><sub><sup><tt><u><var>");
        $text = preg_replace('/( style="[^"]+")/m', '', $text);

        return $text;
    }

    /**
     * Get all HTTPResolverException errors.
     *
     * @param HTTPResolverException $e
     * @return array
     */
    protected function getHTTPExceptionErrors(HTTPResolverException $e)
    {
        $errors = [];
        do {
            $errors[] = ['message' => $e->getMessage(), 'url' => $e->getUrl(), 'HTTPCode' => $e->getHTTPCode()];
        } while ($e = $e->getPrevious());

        return $errors;
    }

    /**
     * Returns TaskRequest collection.
     *
     * @param $dataArray
     * @return \Illuminate\Support\Collection
     */
    protected function save($dataArray)
    {
        $urls = collect();
        foreach ($dataArray as $data)
        {
            $url = TaskRequest::create($data);

            $urlResults = collect();
            foreach ($data['results'] as $result)
                $urlResults->push(TaskResult::make(['result' => $result]));
            $url->results()->saveMany($urlResults);

            $urlConstraints = collect();
            foreach ($data['constraints'] as $constraint)
                $urlConstraints->push(TaskConstraint::make(['constraint' => $constraint]));
            $url->constraints()->saveMany($urlConstraints);

            $urlErrors = collect();
            foreach ($data['errors'] as $error)
                $urlErrors->push(TaskError::make($error));
            $url->errors()->saveMany($urlErrors);

            $urls->push($url);
        }

        return $urls;
    }

}
