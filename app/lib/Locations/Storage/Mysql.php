<?php
namespace Lib\Locations\Storage;

/**
 * Class Mysql
 *
 * Obsługa magazynu danych z Lokalizacjami - w bazie MySQL (wykorzystuje PDO)
 *
 * @package Lib\Locations\Storage
 */
class Mysql implements StorageInterface
{
    const TABLE_NAME_LOCATIONS = 'locations';

    /**
     * @var array
     */
    protected $_pdoConfig = array();

    /**
     * @var \PDO
     */
    static $pdoInstance = null;

    /**
     * @var array
     */
    protected $_locationsCollectionArray = array();


    /**
     * Mysql Storage constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->_pdoConfig = $config;
    }


    /**
     * Metoda ustawia kolekcję lokalizacji, na których zostaną wykonane operacje
     *
     * @param array $collectionData
     */
    public function setCollectionArray($collectionData = array())
    {
        $this->_locationsCollectionArray = $collectionData;
    }

    /**
     * Metoda zatwierdzająca wszystkie zmiany wprowadzone w lokalizacjach
     * w kolekcji - aktualizuje lub dodaje nowe wpisy do bazy
     */
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
                ':distance_from_hq' => $locationObj->getDistanceFromHeadquarters()
            );

            if ($locationId && $locationObj->isModified() && !$locationObj->isDeleted()) {
                $dataArray[':id'] = $locationId;

                // AKTUALIZACJA
                $query = 'UPDATE ' . Mysql::TABLE_NAME_LOCATIONS . ' SET
                    description = :description,
                    address = :address,
                    latitude = :latitude,
                    longitude = :longitude,
                    distance_from_hq = :distance_from_hq
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
                    (description, address, latitude, longitude, distance_from_hq)
                    VALUES (:description, :address, :latitude, :longitude, :distance_from_hq)';

                $stmt = $pdo->prepare($query);
                $stmt->execute($dataArray);

                $locationObj->setId($pdo->lastInsertId());
            }
        }
    }


    /**
     * Metoda pobierająca z bazy rekordy lokalizacji.
     * Opcjonalnie przyjmuje parametry wyszukiwania i sortowania.
     *
     * @param array $params
     *
     * @return array
     */
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

        if ($params['hq']) {
            // proste pobranie pojedynczego wpisu
            $query = 'SELECT * FROM ' . Mysql::TABLE_NAME_LOCATIONS . ' WHERE headquarters = "y"';
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
            $queryWhere[] = '(distance_from_hq <= :distance AND distance_from_hq != -1)';
            $queryData['distance'] = $params['distance_from_hq'];
            $params['order_by'] = 'distance_from_hq';
        }

        if (!empty($params['excludedIds'])) {
            $queryWhere[] = 'id NOT IN (' . implode(',', $params['excludedIds']) . ')';
        }

        if ($params['order_by'] and in_array($params['order_by'], array('id', 'description', 'distance_from_hq'))) {
            $queryOrderBy = ' ORDER BY ' . $params['order_by'] . ' ' . ($params['order'] == 'desc' ? 'desc' : 'asc');
        } else {
            $queryOrderBy = ' ORDER BY description';
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
     * Metoda pomocnicza zwraca instancję PDO
     *
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