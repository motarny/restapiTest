<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 12:29
 */

namespace Lib\Locations;

/**
 * Class Location
 *
 * Klasa typu data object, do przechowywania informacji o lokalizacji
 *
 * @package Lib\Locations
 */
class Location
{
    const STATUS_STANDARD = 1;
    const STATUS_HEADQUARTERS = 2;


    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_description;

    /**
     * @var string
     */
    protected $_address;

    /**
     * @var float
     */
    protected $_latitude;

    /**
     * @var float
     */
    protected $_longitude;

    /**
     * @var int
     */
    protected $_distanceFromHeadquarters = -1;

    /**
     * @var int
     */
    protected $_locationStatus = Location::STATUS_STANDARD;

    protected $_isModified = false;
    protected $_toDelete = false;


    /**
     * Location constructor.
     */
    public function __construct($params = array())
    {
        if ($params) {
            $this->setId($params['id'])
                ->setDescription($params['description'])
                ->setAddress($params['address'])
                ->setLatitude($params['latitude'])
                ->setLongitude($params['longitude'])
                ->setDistanceFromHeadquarters($params['distance_from_headquarters']);

            if ($params['headquarters'] == 'y' || $params['headquarters'] === true) {
                $this->setAsHeadquarters();
            }
        }
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->_id = $id;
        $this->_isModified = true;

        return $this;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        if ($description != $this->_description) {
            $this->_description = $description;
            $this->_isModified = true;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        if ($address != $this->_address) {
            $this->_address = $address;
            $this->_isModified = true;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->_latitude;
    }

    /**
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude($latitude)
    {
        if ($latitude != $this->_latitude) {
            $this->_latitude = floatval($latitude);
            $this->_isModified = true;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->_longitude;
    }

    /**
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude($longitude)
    {
        if ($longitude != $this->_longitude) {
            $this->_longitude = floatval($longitude);
            $this->_isModified = true;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isHeadquarters()
    {
        return $this->_locationStatus == Location::STATUS_HEADQUARTERS;
    }

    /**
     * Metoda ustawia daną lokalizację jako Headquarters
     *
     * @return $this
     */
    public function setAsHeadquarters()
    {
        if (!$this->isHeadquarters()) {
            $this->_locationStatus = Location::STATUS_HEADQUARTERS;
            $this->_isModified = true;

            // todo akcja przeliczenia odleglosci miedzy hq a pozostalymi
        }

        return $this;
    }

    /**
     * @param $distance
     *
     * @return $this
     */
    public function setDistanceFromHeadquarters($distance)
    {
        $this->_distanceFromHeadquarters = $distance;

        return $this;
    }


    /**
     * @return int
     */
    public function getDistanceFromHeadquarters()
    {
        return $this->_distanceFromHeadquarters;
    }


    /**
     * Metoda pomocnicza zwracająca dane Lokalizacji w formie tablicy
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'address' => $this->getAddress(),
            'longitude' => $this->getLongitude(),
            'latitude' => $this->getLatitude(),
            'is_headquarters' => $this->isHeadquarters(),
            'distance_from_headquarters' => $this->getDistanceFromHeadquarters()
        );
    }


    /**
     * Metoda ustawia flagę do usunięcia.
     *
     * @return $this
     */
    public function markAsDeleted()
    {
        $this->_toDelete = true;

        return $this;
    }

    /**
     * Zwraca flagę, czy Lokalizacja jest do usunięcia
     * @return bool
     */
    public function isDeleted()
    {
        return (bool)$this->_toDelete;
    }

    /**
     * Flaga zwracająca informację, czy obiekt był modyfikowany po odczycie z bazy.
     *
     * @return bool
     */
    public function isModified()
    {
        return (bool)$this->_isModified;
    }

}