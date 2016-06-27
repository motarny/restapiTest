<?php
/**
 * Created by PhpStorm.
 * User: Marcin
 * Date: 2016-06-27
 * Time: 13:19
 */

namespace Lib\Locations\Storage;


class Mysql implements StorageInterface
{
    const TABLE_NAME_LOCATIONS = 'locations';

    static $pdoInstance = null;

    protected $_locationsCollectionArray = array();

    protected $_pdoConfig = array();


    public function __construct($config)
    {
        $this->_pdoConfig = $config;
    }


    public function setCollectionArray($collectionData = array())
    {
        $this->_locationsCollectionArray = $collectionData;
    }

    public function flush()
    {
        $pdo = $this->getPdo();

        foreach ($this->_locationsCollectionArray as $locationObj) {
            $locationId = $locationObj->getId();

            $dataArray = array(
                ':description' => $locationObj->getDescription(),
                ':address' => $locationObj->getAddress(),
                ':latitude' => $locationObj->getLatitude(),
                ':longitude' => $locationObj->getLongitude(),
                ':headquarters' => ($locationObj->isHeadquarters() ? 'y' : 'n')
            );

            if ($locationId && $locationObj->isModified() && !$locationObj->isDeleted()) {
                $dataArray[':id'] = $locationId;
                // AKTUALIZACJA
                $query = 'UPDATE ' . Mysql::TABLE_NAME_LOCATIONS . ' SET
                    description = :description,
                    address = :address,
                    latitude = :latitude,
                    longitude = :longitude,
                    headquarters = :headquarters
                    WHERE id = :id';

                $stmt = $pdo->prepare($query);
                $stmt->execute($dataArray);
            } else if ($locationId && $locationObj->isDeleted()) {
                // USUNIECIE
                $query = 'DELETE FROM ' . Mysql::TABLE_NAME_LOCATIONS . ' WHERE id = ?';
                $stmt = $pdo->prepare($query);
                $stmt->execute(array($locationId));
            } else if (!$locationId) {
                // UTWORZENIE NOWEGO
                $query = 'INSERT INTO ' . Mysql::TABLE_NAME_LOCATIONS . '
                    (description, address, latitude, longitude, headquarters)
                    VALUES (:description, :address, :latitude, :longitude, :headquarters)';

                $stmt = $pdo->prepare($query);
                $stmt->execute($dataArray);

                $locationObj->setId($pdo->lastInsertId());
            }
        }
    }


    public function getLocations($params = array())
    {
        $pdo = $this->getPdo();

        if (isset($params['id'])) {
            // proste pobranie pojedynczego wpisu
            $query = 'SELECT * FROM ' . Mysql::TABLE_NAME_LOCATIONS . ' WHERE id = ?';
            $stmt = $pdo->prepare($query);
            $stmt->execute(array($params['id']));

            $getData = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $getData;
        }

        $queryWhere = array();
        $queryData = array();

        if ($params['text']) {
            // to może mieć wiele rozwiązań, wybrałem proste
            $queryWhere[] = 'description LIKE :text OR address LIKE :text';
            $queryData['text'] = '%' . $params['text'] . '%';
        }

        if ($params['distance_from_hq']) {
            $queryWhere[] = 'distance_from_hq <= :distance';
            $queryData['distance'] = $params['distance_from_hq'];
        }

        if (!empty($params['excludedIds'])) {
            $queryWhere[] = 'id NOT IN (' . implode(',', $params['excludedIds']) . ')';
        }

        if ($params['order_by'] and in_array($params['order_by'], array('id', 'description', 'distance_from_hq'))) {
            $queryOrderBy = ' ORDER BY ' . $params['order_by'] . ' ' . ($params['order'] == 'desc' ? 'desc' : 'asc');
        } else {
            $queryOrderBy = ' ORDER BY id';
        }

        $query = 'SELECT * FROM ' . Mysql::TABLE_NAME_LOCATIONS;

        if (!empty($queryWhere)) {
            $query .= ' WHERE ';
        }

        foreach ((array)$queryWhere as $whereCondition) {
            $query .= '(' . $whereCondition . ') AND ';
        }

        $query = trim($query, ' AND ') . $queryOrderBy;

        $stmt = $pdo->prepare($query);
        $stmt->execute($queryData);

        $getLocations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $getLocations;
    }


    /**
     * @return \PDO
     */
    public function getPdo()
    {
        if (self::$pdoInstance) {
            return self::$pdoInstance;
        }

        $pc = $this->_pdoConfig;

        self::$pdoInstance = new \PDO($pc['dsn'], $pc['user'], $pc['password']);

        return self::$pdoInstance;
    }

}