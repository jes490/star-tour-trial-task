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
                $response = $this->getByPhantomJs($url)->getContent();
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
    public function getByCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; U; en; rv:1.8.0) Gecko/20060728 Firefox/1.5.0" );
        
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

        return mb_convert_encoding($response, 'UTF-8', 'windows-1251');
    }

    /**
     * Try resolve url by phantomjs.
     *
     * @param $url
     * @return string
     * @throws HTTPResolverException
     */
    public function getByPhantomJs($url)
    {
        $client = Client::getInstance();
        $client->isLazy();
        $client->getEngine()->setPath(base_path('/vendor/bin/phantomjs'));
        $request = $client->getMessageFactory()->createRequest($url, 'get');
        $request->setTimeout(10000);
        $request->setDelay(5);
        $request->addSetting('userAgent', $this->getUserAgent());
        $response = $client->getMessageFactory()->createResponse();
        $client->send($request, $response);
        
        
        if (($code = $response->getStatus()) !== 200) {
            $this->lastException = (new HTTPResolverException("PhantomJS: cannot get remote content.", 0, $this->lastException))->setUrl($url)->setHTTPCode($code);
            throw $this->lastException;
        }

        return $response->getContent();
    }

    /**
     * Get random user agent. Suggested fix for 408 PhantomJS timeout.
     * 
     * @return mixed
     */
    protected function getUserAgent()
    {
        $chrome_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36';
        $firefox_agent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0';
        $ie_agent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko';
        $edge_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063';
        
        return array_rand([$chrome_agent, $firefox_agent, $ie_agent, $edge_agent]);
    }
}


