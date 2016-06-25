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


    public function getAll()
    {
        return $this->_storageObj->getLocations();
    }


    public function getFiltered($params = array())
    {
        return $this->_storageObj->getLocations($params);
    }

    public function getById($id)
    {
        return $this->_storageObj->getLocations(array(
            'id' => $id
        ));
    }


    public function addLocation(Location $locationObj)
    {
        $this->_storageObj->addLocation($locationObj);
    }

}