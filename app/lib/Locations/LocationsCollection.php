<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 12:37
 */

namespace Lib\Locations;


use Lib\Locations\Storage\StorageInterface;

class LocationsCollection
{
    protected $_storageObj;

    protected $_locationsCollectionArray = array();


    public function __construct(StorageInterface $storageObj)
    {
        $this->_storageObj = $storageObj;
    }


    public function getLocations($params = array())
    {
        $excludeLocationsIdsToLoad = array_keys($this->_locationsCollectionArray);

        $params['excludedIds'] = $excludeLocationsIdsToLoad;

        $locationsData = $this->_storageObj->getLocations($params);

        foreach ($locationsData as $locationData) {
            $locationObj = new Location($locationData);
            $this->_locationsCollectionArray[$locationObj->getId()] = $locationObj;
        }

        return $this->_locationsCollectionArray;
    }

    public function getById($id)
    {
        if ($this->_locationsCollectionArray[$id]) {
            return $this->_locationsCollectionArray[$id];
        }

        $locationData = $this->_storageObj->getLocations(array(
            'id' => $id
        ));

        if (!$locationData) {
            throw new \Exception('Location [' . $id . '] not found', 4);
        }

        $locationObj = new Location($locationData);
        $this->_locationsCollectionArray[$locationObj->getId()] = $locationObj;

        return $locationObj;
    }


    protected function getNotDeleted()
    {
        $returnArray = array();
        foreach ($this->_locationsCollectionArray as $locationObj) {
            if (!$locationObj->isDeleted()) {
                $returnArray[$locationObj->getId()] = $locationObj;
            }
        }

        return $returnArray;
    }


    public function addLocation(Location $locationObj)
    {
        if (!$locationObj->getId()) {
            $locationId = max(array_keys($this->_locationsCollectionArray)); // tymczasowy, przy zapisie do bazy może się zmienić
        } else {
            $locationId = $locationObj->getId();
        }
        $this->_locationsCollectionArray[$locationId] = $locationObj;
    }

    public function deleteLocation(Location $locationObj)
    {
        $locationObj->markAsDeleted();
    }

    public function flush()
    {
        $this->_storageObj->setCollectionArray($this->_locationsCollectionArray);
        $this->_storageObj->flush();
    }

}