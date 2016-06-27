<?php
namespace Lib\Locations;

use Lib\Locations\Storage\StorageInterface;

/**
 * Class LocationsCollection
 *
 * Klasa obsługująca kolekcję Lokalizacji - pobieranie, tworzenie, modyfikowanie.
 * Do działania potrzebuje obiektu typu StorageInterface do którego deleguje
 * konkretne działania na magazynie danych.
 *
 * @package Lib\Locations
 */
class LocationsCollection
{
    /**
     * @var StorageInterface
     */
    protected $_storageObj;

    /**
     * tablica z lokalizacjami w użyciu (utworzone, odczytane)
     *
     * @var array
     */
    protected $_locationsCollectionArray = array();


    /**
     * LocationsCollection constructor.
     *
     * @param StorageInterface $storageObj
     */
    public function __construct(StorageInterface $storageObj)
    {
        $this->_storageObj = $storageObj;
    }


    /**
     * Metoda zwraca listę lokalizacji w formie tablicy.
     *
     * @param array $params opcjonalne parametry wyszukiwania, sortowania itp.
     *
     * @return array
     */
    public function getLocations($params = array())
    {
        // jeśli już coś odczytaliśmy, to nie odczytujemy z bazy ponownie
        $excludeLocationsIdsToLoad = array_keys($this->_locationsCollectionArray);
        $params['excludedIds'] = $excludeLocationsIdsToLoad;

        $locationsData = $this->_storageObj->getLocations($params);

        foreach ($locationsData as $locationData) {
            $locationObj = new Location($locationData);
            $this->_locationsCollectionArray[$locationObj->getId()] = $locationObj;
        }

        return $this->_locationsCollectionArray;
    }

    /**
     * Metoda zwraca Lokalizację na podstawie konkretnego id.
     *
     * @param $id
     *
     * @return Location
     * @throws \Exception
     */
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


    /**
     * Metoda dodaje nową lokalizację do kolekcji
     *
     * @param Location $locationObj
     */
    public function addLocation(Location $locationObj)
    {
        if (!$locationObj->getId()) {
            $locationId = max(array_keys($this->_locationsCollectionArray)); // tymczasowy, przy zapisie do bazy może się zmienić
        } else {
            $locationId = $locationObj->getId();
        }
        $this->_locationsCollectionArray[$locationId] = $locationObj;
    }


    /**
     * Metoda usuwa wskazaną lokalizację (ustawia flagę do usunięcia!)
     *
     * @param Location $locationObj
     */
    public function deleteLocation(Location $locationObj)
    {
        $locationObj->markAsDeleted();
    }

    /**
     * Metoda zatwierdzająca zmiany w kolekcji.
     * Aby zapisać do bazy (lub innego storage) wprowadzone zmiany (dodawanie, modyfikacja,
     * usuwanie kolekcji) należy wywołać tą metodę.
     */
    public function flush()
    {
        $this->_storageObj->setCollectionArray($this->_locationsCollectionArray);
        $this->_storageObj->flush();
    }

}