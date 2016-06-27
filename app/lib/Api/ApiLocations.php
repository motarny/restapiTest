<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-27
 * Time: 00:51
 */

namespace Lib\Api;


use Lib\Locations\LocationsCollection;
use Lib\Locations\Storage\PhpArray;
use Lib\Request\RequestReader;
use Lib\Response\Response;

class ApiLocations extends Api
{
    static function run(RequestReader $requestObj, Response $responseObj)
    {
        $requestData = $requestObj->getRequestData('resourcesFullQuery');

        var_dump($requestData);

        if ($requestObj->isGet()) {
            // GET
            if (!$requestData[1]) {
                // pobranie listy lokalizacji
                $locationsCollection = new LocationsCollection(new PhpArray());
                if ($requestObj->getRequestData('queryParams')) {
                    $getLocations = $locationsCollection->getFiltered($requestObj->getRequestData('queryParams'));
                    $responseObj->setResponseBodySuccess(array('action' => 'lista lokalizacji z jakims filtrem'));
                } else {
                    $getLocations = $locationsCollection->getAll();
                    $responseObj->setResponseBodySuccess(array('action' => 'lista lokalizacji bez filtra'));
                }
            } else {
                // pobranie informacji o konkretnej lokalizacji
                $responseObj->setResponseBodySuccess(array('action' => 'pobranie info o lokalizacji: ' . $requestData[1]));
            }
        }

        if ($requestObj->isPost()) {
            // POST
            if (!$requestData[1]) {
                // utworzenie nowej lokalizacji
                $responseObj->setResponseBodySuccess(array('action' => 'utworzenie nowej lokalizacji'));
            } else {
                // aktualizacja lokajizacji (analogicznie jak PUT)
                $responseObj->setResponseBodySuccess(array('action' => 'aktualizacja lokalizacji ' . $requestData[1]));
            }
        }

    }

}