<?php
namespace Lib\Request;

use Lib\Api\Api;

/**
 * Class RequestReader
 *
 * Klasa odczytująca parametry z requesta. Singleton
 *
 * @package Lib\Request
 */
class RequestReader
{
    /**
     * @var RequestReader
     */
    static $requestInstance = null;

    /**
     * @var array
     */
    protected $_requestData = array();


    /**
     * RequestReader constructor.
     * Prywatna, wymuszamy tworzenie przez ::instance();
     */
    private function __construct()
    {
    }

    /**
     * Generuje i/lub zwraca Singleton
     *
     * @return RequestReader
     * @throws \Exception
     */
    static function instance()
    {
        if (!self::$requestInstance) {
            self::$requestInstance = new RequestReader();
            self::$requestInstance->init();
        }

        return self::$requestInstance;
    }


    /**
     * Metoda robocza odczytu parametrów requesta
     *
     * @throws \Exception
     */
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


    /**
     * Zwraca metodę requesta (get/post/put/delete...)
     * @return mixed
     */
    public function getHttpMethod()
    {
        return $this->_requestData['method'];
    }


    /**
     * Metoda zwraca nazwę zasobu do jakiego chcemy uzyskać dostęp.
     * Jest to pierwszy parametr w ścieżce.
     *
     * @return string
     */
    public function resourcesRequest()
    {
        return $this->_requestData['resourcesRequest'];
    }


    /**
     * Zwraca wskazany parametr requesta
     * @param $paramName
     *
     * @return array
     */
    public function getRequestData($paramName) {
        if ($paramName) {
            return $this->_requestData[$paramName];
        }

        return $this->_requestData;
    }

    /**
     * Zwraca wskazaną wartość przekazaną do requesta
     *
     * @param $paramName
     *
     * @return mixed
     */
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