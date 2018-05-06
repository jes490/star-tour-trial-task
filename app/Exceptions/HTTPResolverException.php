<?php

namespace App\Exceptions;

use Exception;

class HTTPResolverException extends Exception
{
    /**
     * URL where exception happens.
     *
     * @var
     */
    private $url;

    /**
     * HTTP status code.
     *
     * @var
     */
    private $HTTPcode;

    /**
     * Set error url.
     *
     * @param $url
     * @return $this
     */
    public function setURL($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set error HTTP code.
     *
     * @param $code
     * @return $this
     */
    public function setHTTPCode($code)
    {
        $this->HTTPcode = $code;

        return $this;
    }

    /**
     * Get URL of error.
     *
     * @return mixed
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * Get code of error.
     *
     * @return mixed
     */
    public function getHTTPCode()
    {
        return $this->HTTPcode;
    }
}
