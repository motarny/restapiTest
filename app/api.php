<?php

use \Lib\Response\Response;
use \Lib\Request\RequestReader;
use \Lib\Api\Api;

require_once 'autoloader.php';

// inicjacja responsa - zawsze coś musimy zwrócić
$response = Response::instance();

// inicjacja - odczyt requesta
$request = RequestReader::instance();

// wykonanie oczekiwanej akcji
try {
    switch ($request->resourcesRequest()) {
        case 'locations' :
            \Lib\Api\ApiLocations::run($request, $response);
            break;

        default :
            $response->setResponseBodyError(2, 'Invalid resource request: [' . $request->resourcesRequest() . ']');
    }
} catch (Exception $e) {
    $response->setResponseBodyError($e->getCode(), $e->getMessage());
}


$response->setHeaders();

echo json_encode($response->getBody());