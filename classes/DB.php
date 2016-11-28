<?php

namespace classes;

use \PDO;
use \PDOException;

/**
 * Class DB
 *
 * Понимаю, что синглтон местами признаётся антипаттерном, но для демонстрации знаний вполне сгодится
 *
 * @package classes
 */
class DB
{
    const USERNAME = 'root';

    const PASSWORD = 'root';

    const NAME = 'red_crown';

    const HOST = 'localhost';

    /**
     * @var \PDO
     */
    private $connection;

    private static $o;

    private function __construct()
    {
        try {
            $this->connection = new PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME, self::USERNAME, self::PASSWORD);
        } catch (PDOException $e) {
            die('MySQL connection failed: ' . $e->getMessage());
        }

        $this->connection->prepare("SET NAMES uft8")->execute();
    }

    private function __clone() {}

    public static function o()
    {
        if (is_null(self::$o)) {
            self::$o = new self;
        }

        return self::$o;
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public function checkTableExistence($tableName)
    {
        $sql = "SHOW TABLES LIKE :tableName";

        $tables = $this->fetchAll($sql, [':tableName' => $tableName]);

        return !empty($tables);
    }

    /**
     * @param $tableName
     * @param $fields
     * @return bool
     */
    public function createTable($tableName, $fields)
    {
        $tableName = preg_replace('/[^a-z0-9_]/', '', $tableName);

        $fieldStrings = [];
        foreach ($fields as $fieldName => $type) {
            $fieldStrings[] = $this->clearString($fieldName) . ' ' . $type;
        }

        $sql = "CREATE TABLE $tableName (";
        $sql .= implode(', ', $fieldStrings);
        $sql .= ")";

        return $this->exec($sql, []);
    }

    public function importDataFromCsv($tableName, $filePath)
    {
        $tableName = $this->clearString($tableName);
        $data = file($filePath);

        foreach ($data as $row) {
            $sql = "INSERT INTO $tableName VALUES";
            $rowParts = explode(';', $row);
            $vars = array_fill(0, count($rowParts), '?');
            $sql .= '(NULL, ' . implode(', ', $vars) . ')';

            $this->exec($sql, $rowParts);
        }
    }

    public function updateById($tableName, $data, $id)
    {
        $tableName = $this->clearString($tableName);

        $sql = "UPDATE $tableName SET ";

        $params = [];
        $sqlParts = [];
        foreach ($data as $field => $value) {
            $field = $this->clearString($field);
            $sqlParts[] = $field . ' = :' . $field;
            $params[':' . $field] = $value;
        }

        $sql .= implode(',', $sqlParts);
        $sql .= ' WHERE id = :id';

        $params[':id'] = $id;

        return $this->exec($sql, $params);
    }

    /**
     * @param $tableName
     * @return bool
     */
    public function getRandomRow($tableName)
    {
        $tableName = $this->clearString($tableName);

        $sql = "SELECT * FROM $tableName ORDER BY RAND() LIMIT 1";

        return array_shift($this->fetchAll($sql, []));
    }

    /**
     * @param $sql
     * @param $params
     * @return array
     */
    private function fetchAll($sql, $params)
    {
        $query = $this->connection->prepare($sql, $params);

        $query->execute($params);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @param $params
     * @return bool
     */
    private function exec($sql, $params)
    {
        $query = $this->connection->prepare($sql);
        return $query->execute($params);
    }

    /**
     * Не помню, как штатными средставами это правильно заэкранировать
     *
     * @param $string
     * @return mixed
     */
    private function clearString($string)
    {
        return preg_replace('/[^a-z0-9_]/', '', $string);
    }
}
