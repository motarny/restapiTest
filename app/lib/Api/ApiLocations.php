<?php
namespace Lib\Api;

use Lib\Locations\Location;
use Lib\Locations\LocationsCollection;
use Lib\Locations\Storage\Mysql;
use Lib\Locations\Storage\StorageInterface;
use Lib\Request\RequestReader;
use Lib\Response\Response;

/**
 * Class ApiLocations
 *
 * Klasa odpowiadająca za realizację wszystkich żądań odnośnie zasobów Locations
 */
class ApiLocations
{
    /**
     * Metoda statyczna uruchamiana przez bramkę api.
     *
     * @param RequestReader    $requestObj
     * @param Response         $responseObj
     * @param StorageInterface $storageObj
     *
     * @throws \Exception
     */
    static function run(RequestReader $requestObj, Response $responseObj, StorageInterface $storageObj)
    {
        // pobranie całej ścieżki requesta
        $requestData = $requestObj->getRequestData('resourcesFullQuery');

        // drugi parametr w ścieżce to id lokalizacji (/locations/#)
        $locationId = $requestData[1];

        $locationsCollection = new LocationsCollection($storageObj);

        // request - method GET
        if ($requestObj->isGet()) {
            if (isset($locationId)) {
                // jeśli podano id lokalizacji w requeście, to próbujemy odczytać dane konkretnej lokalizacji
                try {
                    $getLocation = $locationsCollection->getById($locationId);
                    $responseObj->setResponseBodySuccess($getLocation->toArray());
                } catch (\Exception $e) {
                    $responseObj->setHeaderHttpCode(404);
                    throw $e;
                }
            } else {
                // nie podano id lokalizacji - pobranie listy lokalizacji z opcjonalnymi filtrami
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

        // request - method POST
        if ($requestObj->isPost()) {
            if ($locationId) {
                // jeśli podano id lokalizacji, to jeśli taka istnieje - aktualizujemy ją
                try {
                    $locationObj = $locationsCollection->getById($locationId);
                } catch (\Exception $e) {
                    $responseObj->setHeaderHttpCode(404);
                    throw $e;
                }
                $action = 'update';
            } else {
                // nie przekazano id - tworzenie nowej lokalizacji
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

            // jeśli wszystko przebiegło ok, zwracamy informację wraz z id utworzonego / zaktualizowanego wpisu
            $responseObj->setResponseBodySuccess(array('action' => $action, 'id' => $locationObj->getId()));
        }

        // request - method DELETE
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