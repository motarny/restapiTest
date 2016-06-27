<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-27
 * Time: 00:51
 */

namespace Lib\Api;


use Lib\Locations\Location;
use Lib\Locations\LocationsCollection;
use Lib\Locations\Storage\Mysql;
use Lib\Locations\Storage\StorageInterface;
use Lib\Request\RequestReader;
use Lib\Response\Response;

class ApiLocations
{
    static function run(RequestReader $requestObj, Response $responseObj, StorageInterface $storageObj)
    {
        $requestData = $requestObj->getRequestData('resourcesFullQuery');
        $locationId = $requestData[1];

        $locationsCollection = new LocationsCollection($storageObj);

        if ($requestObj->isGet()) {
            // GET
            if (isset($locationId)) {
                // pobranie informacji o konkretnej lokalizacji
                try {
                    $getLocation = $locationsCollection->getById($locationId);
                    $responseObj->setResponseBodySuccess($getLocation->toArray());
                } catch (\Exception $e) {
                    $responseObj->setHeaderHttpCode(404);
                    $responseObj->setResponseBodyError($e->getCode(), $e->getMessage());
                }
            } else {
                // pobranie listy lokalizacji
                $getLocations = $locationsCollection->getLocations($requestObj->getRequestData('queryParams'));

                $returnArray = array();
                if (count($getLocations) > 0) {
                    foreach ($getLocations as $locationObj) {
                        $returnArray[] = $locationObj->toArray();
                    }
                    $responseObj->setResponseBodySuccess(array('count' => count($returnArray), 'locations' => $returnArray));
                }
            }
        }

        if ($requestObj->isPost()) {
            // POST
            $action = '';
            if ($locationId) {
                // aktualizacja lokajizacji
                try {
                    $locationObj = $locationsCollection->getById($locationId);
                } catch (\Exception $e) {
                    $responseObj->setHeaderHttpCode(404);
                    throw $e;
                }
                $action = 'update';
            } else {
                // tworzenie nowej lokalizacji
                $locationObj = new Location();
                $locationsCollection->addLocation($locationObj);
                $action = 'create';
            }

            $locationObj->setDescription($requestObj->getRequestParam('description'))
                ->setAddress($requestObj->getRequestParam('address'))
                ->setLatitude($requestObj->getRequestParam('latitude'))
                ->setLongitude($requestObj->getRequestParam('longitude'));

            if ($requestObj->getRequestParam('is_headquarters') == 'y') {
                $locationObj->setAsHeadquarters();
            }

            $locationsCollection->flush();

            $responseObj->setResponseBodySuccess(array('action' => $action, 'id' => $locationObj->getId()));
        }


        if ($requestObj->isDelete()) {
            if (!$locationId) {
                throw new \Exception("Location id is required", 3);
            }

            // usun lokalizacje
            try {
                $locationObj = $locationsCollection->getById($locationId);
            } catch (\Exception $e) {
                $responseObj->setHeaderHttpCode(404);
                throw $e;
            }

            $locationsCollection->deleteLocation($locationObj);
            $locationsCollection->flush();

            $responseObj->setResponseBodySuccess(array('action' => 'delete', 'id' => $locationId));
        }

    }

}