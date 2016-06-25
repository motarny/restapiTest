<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 13:11
 */

namespace Lib\Locations\Storage;


use Lib\Locations\Location;

class PhpArray implements StorageInterface
{
    protected $_locationsArray = array();


    public function getLocations($params = array())
    {
        if (empty($params)) {
            return $this->_locationsArray;
        }
    }

    public function addLocation(Location $locationObj)
    {
        $locationId = $locationObj->getId();

        if (!$locationId) {
            $locationId = $this->getLastId() + 1;
             $locationObj->setId($locationId);
        }

        $this->_locationsArray[$locationId] = $locationObj;
    }

    private function getLastId()
    {
        $maxId = 0;
        foreach ($this->_locationsArray as $locationObj) {
            $locId = $locationObj->getId();
            if ($locId > $maxId) {
                $maxId = $locId;
            }
        }

        return $maxId;
    }


    public function flush()
    {
    }

}