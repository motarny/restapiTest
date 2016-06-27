<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-26
 * Time: 22:05
 */

namespace Lib\Request;

use Lib\Api\Api;

class RequestReader
{
    static $requestInstance = null;

    protected $_requestData = array();


    private function __construct()
    {
    }

    static function instance()
    {
        if (!self::$requestInstance) {
            self::$requestInstance = new RequestReader();
            self::$requestInstance->init();
        }

        return self::$requestInstance;
    }


    protected function init()
    {
        parse_str($_SERVER['QUERY_STRING'], $queryParams);
        $this->_requestData = array(
            'method' => strtoupper($_SERVER['REQUEST_METHOD']),
            'queryParams' => $queryParams,
            'requestParams' => $_REQUEST,
            'accept' => $_SERVER['HTTP_ACCEPT'],
        );

        $pathParams = array();
        if ($_SERVER['PATH_INFO']) {
            // jeśli uruchamiane z api.php/... (standardowo)
            $pathInfo = rtrim($_SERVER['PATH_INFO'], DIRECTORY_SEPARATOR);
            $pathParams = explode(DIRECTORY_SEPARATOR, $pathInfo);
            array_shift($pathParams);
        } else {
            // todo opcjonalnie do parsowania REQUEST_URI
        }

        if (empty($pathParams)) {
            throw new \Exception('I need some params!', 1);
        }

        $resourceRequestName = strtolower($pathParams[0]);
        $this->_requestData['resourcesRequest'] = $resourceRequestName;
        $this->_requestData['resourcesFullQuery'] = $pathParams;
    }


    public function getHttpMethod()
    {
        return $this->_requestData['method'];
    }

    public function resourcesRequest()
    {
        return $this->_requestData['resourcesRequest'];
    }

    public function getRequestData($paramName) {
        if ($paramName) {
            return $this->_requestData[$paramName];
        }

        return $this->_requestData;
    }

    public function getRequestParam($paramName) {
        return $this->_requestData['requestParams'][$paramName];
    }

    public function isGet()
    {
        return $this->getHttpMethod() === 'GET';
    }

    public function isPost()
    {
        return $this->getHttpMethod() === 'POST';
    }

    public function isPut()
    {
        return $this->getHttpMethod() === 'PUT';
    }

    public function isDelete()
    {
        return $this->getHttpMethod() === 'DELETE';
    }

}