<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 12:48
 */

namespace Lib\Locations\Storage;


use Lib\Locations\Location;

interface StorageInterface
{

    function getLocations($params = array());

    function addLocation(Location $locationObj);

    function flush();

}