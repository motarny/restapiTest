<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 12:29
 */

namespace Lib\Locations;
use Lib\Google\GoogleApiUsage;

/**
 * Class Location
 *
 * Klasa typu data object, do przechowywania informacji o lokalizacji
 *
 * @package Lib\Locations
 */
class Location
{
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
     * Tablica z danymi pobranymi z google api
     * @var array
     */
    protected $_geoData = array();

    /**
     * @var int
     */
    protected $_distanceFromHeadquarters = -1;

    /**
     * @var string
     */
    protected $_headquarters = 'n';

    /**
     * kilka przydatnych flag
     */
    protected $_isModified = false;
    protected $_isGeoModified = false;
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
                ->setHeadquatersFlag($params['headquarters'])
                ->setDistanceFromHeadquarters($params['distance_from_hq']);
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
        if ($this->_description && $description != $this->_description) {
            $this->_isModified = true;
        }
        $this->_description = $description;

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
        if ($this->_address && $address != $this->_address) {
            $this->_isModified = true;
        }
        $this->_address = $address;

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
        if (!$latitude) {
            $latitude = $this->getGetoData('lat');
        }
        if ($this->_latitude && $latitude != $this->_latitude) {
            $this->_isModified = true;
            $this->_isGeoModified = true;
            $this->setDistanceFromHeadquarters(-1);
        }
        $this->_latitude = floatval($latitude);

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
        if (!$longitude) {
            $longitude = $this->getGetoData('lng');
        }
        if ($this->_longitude && $longitude != $this->_longitude) {
            $this->_isGeoModified = true;
            $this->_isModified = true;
            $this->setDistanceFromHeadquarters(-1);
        }
        $this->_longitude = floatval($longitude);

        return $this;
    }


    /**
     * Pobiera informacje lokalizacyjne
     *
     * @param $param
     *
     * @return mixed
     */
    protected function getGetoData($param)
    {
        if (!$this->_geoData) {
            $this->_geoData = GoogleApiUsage::getLocationInfo($this);
        }

        return $this->_geoData[$param];

    }


    /**
     * Ustawia flagę, czy jest to siedziba
     * @param $flag
     *
     * @return $this
     */
    public function setHeadquatersFlag($flag)
    {
        $this->_headquarters = $flag;

        return $this;
    }


    /**
     * @return bool
     */
    public function isHeadquarters()
    {
        return $this->_headquarters == 'y';
    }


    /**
     * @param $distance
     *
     * @return $this
     */
    public function setDistanceFromHeadquarters($distance)
    {
        if ($this->_distanceFromHeadquarters && $distance != $this->_distanceFromHeadquarters) {
            $this->_distanceFromHeadquarters = $distance;
            $this->_isModified = true;
        }

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
            'distance_from_hq' => $this->getDistanceFromHeadquarters()
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
     *
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


    /**
     * Flaga zwracająca informację, czy były modyfikowane dane lokalizacyjne
     *
     * @return bool
     */
    public function isGeoModified()
    {
        return (bool)$this->_isGeoModified;
    }

}