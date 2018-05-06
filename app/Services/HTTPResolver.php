<?php

namespace App\Services;

use App\Exceptions\HTTPResolverException;
use JonnyW\PhantomJs\Client;

/**
 * Class HTTPResolver.
 *
 * @package App\Services
 * @author Alexey Sesyolkin
 * @email aseselkin@gmail.com
 */
class HTTPResolver
{
    /**
     * Last thrown exception for exception chain.
     *
     * @var
     */
    protected $lastException;

    /**
     * Resolve url and get content of the page.
     *
     * @param $url
     * @return bool|string
     * @throws HTTPResolverException
     */
    public function resolve($url)
    {
        try {
            $response = $this->getByCurl($url);
        } catch (HTTPResolverException $curlException) {
            try {
                $response = $this->getByPhantomJs($url);
            } catch (HTTPResolverException $PJSException) {
                throw $PJSException;
            }
        }

        return $response;
    }

    /**
     * Try resolve url by curl.
     *
     * @param $url
     * @return string
     * @throws HTTPResolverException
     */
    protected function getByCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        $response = curl_exec($curl);

        if (($code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) !== 200) {
            $this->lastException = (new HTTPResolverException("CURL: cannot get remote content.", 0, $this->lastException))->setURL($url)->setHTTPCode($code);
            throw $this->lastException;
        }
        if (curl_error($curl)) {
            $this->lastException = (new HTTPResolverException('CURL: ' . curl_error($curl), 0, $this->lastException))->setURL($url);
            throw $this->lastException;
        }


        curl_close($curl);

        return $response;
    }

    /**
     * Try resolve url by phantomjs.
     *
     * @param $url
     * @return string
     * @throws HTTPResolverException
     */
    protected function getByPhantomJs($url)
    {
        $client = Client::getInstance();
        $client->getEngine()->setPath(base_path('/vendor/bin/phantomjs'));
        $request = $client->getMessageFactory()->createRequest($url, 'get');
        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);

        if (($code = $response->getStatus()) !== 200) {
            $this->lastException = (new HTTPResolverException("PhantomJS: cannot get remote content.", 0, $this->lastException))->setUrl($url)->setHTTPCode($code);
            throw $this->lastException;
        }

        return $response;
    }
}


