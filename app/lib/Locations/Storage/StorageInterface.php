<?php
namespace Lib\Locations\Storage;

use Lib\Locations\Location;

/**
 * Interface StorageInterface
 *
 * @package Lib\Locations\Storage
 */
interface StorageInterface
{
    function setCollectionArray($collectionData = array());

    function getLocations($params = array());

    function flush();

}