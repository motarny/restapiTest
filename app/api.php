<?php

use \Lib\Response\Response;
use \Lib\Request\RequestReader;
use \Lib\Locations\Storage\Mysql;

require_once 'autoloader.php';

// inicjacja - odczyt requesta
$request = RequestReader::instance();

// inicjacja responsa - zawsze coś musimy zwrócić
$response = Response::instance();
$response->setHeaderContentType($request->getRequestData('accept'));

$pdoConfig = array(
    'dsn' => 'mysql:dbname=***;host=***',
    'user' => '***',
    'password' => '***'
);

$storageObj = new Mysql($pdoConfig);

try {
    switch ($request->resourcesRequest()) {
        case 'locations' :
            \Lib\Api\ApiLocations::run($request, $response, $storageObj);
            break;

        default :
            $response->setResponseBodyError(2, 'Invalid resource request: [' . $request->resourcesRequest() . ']');
    }
} catch (Exception $e) {
    $response->setResponseBodyError($e->getCode(), $e->getMessage());
}

$response->setHeaders();

// todo mozna jakies dekoratory albo proste ify + trochę kodu aby obsłużyć inne formaty odpowiedzi
// na podstawie nagłówka Accept. Dla uproszczenia zwracamy tylko jsona
echo json_encode($response->getBody());

