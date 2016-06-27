<?php
namespace Lib\Response;
use Lib\Request\RequestReader;

/**
 * Class Response
 *
 * Klasa generuje response dla api. Singleton.
 *
 * @package Lib\Response
 */
class Response
{
    /**
     * @var Response
     */
    static $responseInstance = null;

    protected $_responseHeader = array();
    protected $_responseBodyValue = array();
    protected $_responseBodyError = array();
    protected $_responseBodyStatus = '';


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
     * @return Response
     * @throws \Exception
     */
    static function instance()
    {
        if (!self::$responseInstance) {
            self::$responseInstance = new Response();
        }

        return self::$responseInstance;
    }


    /**
     * Metoda ustawia w headerze odpowiedni kod http
     *
     * @param $code
     *
     * @return $this
     */
    public function setHeaderHttpCode($code)
    {
        http_response_code($code);

        return $this;
    }

    /**
     * Metoda ustawia w headerze odpowiedni Content-Type
     *
     * @param $contentType
     *
     * @return $this
     */
    public function setHeaderContentType($contentType)
    {
        $this->_responseHeader['Content-Type'] = $contentType;

        return $this;
    }


    /**
     * Metoda ustawia w headerze parametr Location
     *
     * @param $location
     *
     * @return $this
     */
    public function setHeaderLocation($location)
    {
        $this->_responseHeader['Location'] = $location;

        return $this;
    }


    /**
     * Metoda ustawia treść odpowiedzi jeśli żądanie kończy się powodzeniem
     *
     * @param array $responseData
     *
     * @return $this
     */
    public function setResponseBodySuccess($responseData = array())
    {
        $this->_responseBodyStatus = 'ok';
        $this->_responseBodyValue = $responseData;

        return $this;
    }

    /**
     * Metoda ustawia treść odpowiedzi jeśli żądanie kończy się błędem
     *
     * @param array $responseData
     *
     * @return $this
     */
    public function setResponseBodyError($errorCode, $errorMessage)
    {
        $this->_responseBodyStatus = 'error';
        $this->_responseBodyError = array(
            'number' => $errorCode,
            'message' => $errorMessage
        );

        return $this;
    }


    /**
     * Metoda ustawia nagłówki http
     */
    public function setHeaders()
    {
        foreach ($this->_responseHeader as $paramName => $paramValue) {
            header($paramName . ': ' . $paramValue);
        }
    }


    /**
     * Metoda zwraca odpowiednio sformatowaną treść jaka zostanie zwrócona
     *
     * @return array
     */
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