<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-25
 * Time: 12:29
 */

namespace Lib\Locations;


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
    protected $_status = Location::STATUS_STANDARD;


    /**
     * Location constructor.
     */
    public function __construct()
    {
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
     */
    public function setDescription($description)
    {
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
     */
    public function setAddress($address)
    {
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
     */
    public function setLatitude($latitude)
    {
        $this->_latitude = $latitude;

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
     */
    public function setLongitude($longitude)
    {
        $this->_longitude = $longitude;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHeadquarters()
    {
        return $this->_status == Location::STATUS_HEADQUARTERS;
    }

    public function setAsHeadquarters()
    {
        $this->_status = Location::STATUS_HEADQUARTERS;

        return $this;
    }


}