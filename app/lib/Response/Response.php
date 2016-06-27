<?php
namespace Lib\Response;


class Response
{
    static $responseInstance = null;

    protected $_responseHeader = array();
    protected $_responseBodyValue = array();
    protected $_responseBodyError = array();
    protected $_responseBodyStatus = '';

    private function __construct()
    {
    }

    static function instance()
    {
        if (!self::$responseInstance) {
            self::$responseInstance = new Response();
        }

        return self::$responseInstance;
    }

    public function setHeaderHttpCode($code)
    {
        http_response_code($code);

        return $this;
    }

    public function setHeaderContentType($contentType)
    {
        $this->_responseHeader['Content-Type'] = $contentType;

        return $this;
    }

    public function setHeaderLocation($location)
    {
        $this->_responseHeader['Location'] = $location;

        return $this;
    }

    public function setResponseBodySuccess($responseData = array())
    {
        $this->_responseBodyStatus = 'ok';
        $this->_responseBodyValue = $responseData;

        return $this;
    }

    public function setResponseBodyError($errorCode, $errorMessage)
    {
        $this->_responseBodyStatus = 'error';
        $this->_responseBodyError = array(
            'number' => $errorCode,
            'message' => $errorMessage
        );

        return $this;
    }

    public function setHeaders()
    {
        foreach ($this->_responseHeader as $paramName => $paramValue) {
            header($paramName . ': ' . $paramValue);
        }
    }

    public function getBody()
    {
        $responseBodyArray = array();
        $responseBodyArray['status'] = $this->_responseBodyStatus;
        if ($this->_responseBodyError) {
            $responseBodyArray['error'] = $this->_responseBodyError;
        } else {
            $responseBodyArray['value'] = $this->_responseBodyValue;
        }

        return $responseBodyArray;

    }



}