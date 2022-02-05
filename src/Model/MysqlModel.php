<?php

namespace Vapita\Model;

use DateTime;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;
use ReflectionException;
use stdClass;

class MysqlModel extends PDO implements ModelInterface
{
    public string $dsnString;

    /**
     * @param array $dsn
     * @param string|null $username
     * @param string|null $password
     * @param string|null $options
     * @param string $type
     */
    public function __construct(array $dsn = [], string $username = null, string $password = null, string $options = null, string $type = 'mysql')
    {
        try {
            $this->dsnString = "{$type}:";
            $i = 0;
            foreach ($dsn as $key => $value) {
                $this->dsnString .= "{$key}={$value}";
                if (++$i !== count($dsn)) $this->dsnString .= ";";
            }

            parent::__construct($this->dsnString, $username, $password);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
            $this->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

        } catch (PDOException $e) {
            echo 'Exception abgefangen: ' . $e->getMessage() . "\n";
        }

    }

    /**
     * @param string $preparedStatement
     * @param array $data
     * @param $mode
     * @return false|PDOStatement
     */
    private function select(string $preparedStatement, array $data = [], $mode = null)
    {
        $statement = $this->prepare($preparedStatement);
        foreach ($data as $key => $value) {
            switch ($value) {
                case $value instanceof DateTime:
                    $value = $value->getTimestamp();
                    break;
                case is_string($value):
                    break;
                case is_numeric($value):
                    $value = strval($value);
                    break;
            }
            $statement->bindValue(':' . $key, $value);
        }
        $this->execute($statement);
        return $statement;
    }

    /**
     * @param $id
     * @param string $entity
     * @return false|mixed|object|string
     */
    public function find(string $entity, $id)
    {
        $preparedStatement = " id = :id ";
        try {
            $entityClass = self::setEntityClass($entity);
            $columns = self::setColumns($entityClass);
            $result = self::select("SELECT {$columns} FROM {$entityClass->getShortName()} WHERE {$preparedStatement}", ['id' => $id]);
            return $result->fetchObject($entity);
        } catch (PDOException $exception) {
            return $exception->getMessage();
        } catch (ReflectionException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $entity
     * @param array $sortBy
     * @return array|false|string
     */
    public function findAll(string $entity, array $sortBy = [])
    {
        try {
            $entityClass = self::setEntityClass($entity);
            $columns = self::setColumns($entityClass);
            $orderData = self::createOrderData($sortBy);
            $result = self::select("SELECT {$columns} FROM {$entityClass->getShortName()} $orderData");
            return $result->fetchAll(PDO::FETCH_CLASS,$entity);
        } catch (PDOException $exception) {
            return $exception->getMessage();
        } catch (ReflectionException $e) {
            echo $e->getMessage();
            return false;
        }

    }

    /**
     * @param string $table
     * @param array $data
     * @param array $sortBy
     * @param $mode
     * @return array|false|string
     */
    public function findBy(string $table, array $data, array $sortBy = [], $mode = PDO::FETCH_OBJ)
    {
        $preparedStatement = "";
        $i = 0;
        $dataLength = count($data);
        foreach ($data as $field => $value) {
            $preparedStatement .= " {$field} = :{$field} ";
            if (++$i !== $dataLength) $preparedStatement .= " AND ";
        }
        try {
            $orderData = self::createOrderData($sortBy);
            $result = self::select("SELECT * FROM $table WHERE ($preparedStatement) $orderData", $data);
            return $result->fetchAll($mode);
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param string $table
     * @param array $data
     * @param $mode
     * @return false|mixed|object|stdClass|string
     */
    public function findOneBy(string $table, array $data, $mode = null)
    {
        $preparedStatement = "";
        $i = 0;
        $dataLength = count($data);
        foreach ($data as $field => $value) {
            $preparedStatement .= " {$field} = :{$field} ";
            if (++$i !== $dataLength) $preparedStatement .= " AND ";
        }
        try {
            $result = self::select("SELECT * FROM {$table} WHERE ({$preparedStatement}) LIMIT 0,1", $data, $mode);
            if (false === $object = $result->fetchObject($mode)) return new stdClass();
            return $object;
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param PDOStatement $statement
     * @return bool
     */
    public function execute(PDOStatement $statement): bool
    {
        return $statement->execute();
    }

    /**
     * @param $sortBy
     * @return string
     */
    private function createOrderData($sortBy): string
    {
        $orderData = "";
        if ($sortBy) {
            $orderData .= " ORDER BY ";
            $i = 0;
            foreach ($sortBy as $column => $direction) {
                $orderData .= "$column $direction";
                $orderData .= (++$i === count($sortBy)) ? ';' : ',';
            }
        }
        return $orderData;
    }

    /**
     * @param string $table
     * @param array $data
     * @param int|null $id
     * @return int|string
     */
    public function persist(string $table, array $data, int $id = null)
    {
        if ($id) {
            if (false !== $result = self::select("SELECT * FROM $table WHERE id = :id", ['id' => $id])) {
                return self::update($table, $data, ['id' => $id]);
            }
        }
        return self::insert($table, $data);
    }

    /**
     * @param string $table
     * @param array $data
     * @return string
     */
    public function insert(string $table, array $data): string
    {
        ksort($data);

        $table = strtolower($table);
        $fieldNames = implode(', ', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $stmt = self::prepare(" INSERT INTO $table ($fieldNames) VALUES ($fieldValues) ");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        self::execute($stmt);
        return $this->lastInsertId();
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */
    public function update(string $table, array $data, array $where): int
    {
        ksort($data);

        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :field_$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :where_$key";
            } else {
                $whereDetails .= " AND $key = :where_$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        $stmt = self::prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        self::execute($stmt);
        return $stmt->rowCount();
    }

    /**
     * @param string $table
     * @param array $data
     * @param int $limit
     * @return int
     */
    public function delete(string $table, array $data, int $limit = 1): int
    {
        ksort($data);

        $whereDetails = null;
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        //if limit is a number use a limit on the query
        $useLimit = "";
        if (is_numeric($limit)) {
            $useLimit = "LIMIT $limit";
        }

        $stmt = self::prepare("DELETE FROM $table WHERE $whereDetails $useLimit");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        self::execute($stmt);
        return $stmt->rowCount();
    }


    /**
     * @throws ReflectionException
     */
    private function setEntityClass($entity): ReflectionClass
    {
        if(!class_exists($entity)) throw new ReflectionException();
        return new ReflectionClass($entity);
    }

    private function setColumns(ReflectionClass $entityClass):string
    {
        $entityProperties = $entityClass->getProperties();
        $columns = false;
        foreach ($entityProperties as $property) {
            $propertyNameAsArray = preg_split('/(?=[A-Z])/', $property->getName());
            $propertyNameAsSnakeTail = strtolower(implode('_', $propertyNameAsArray));
            $columns .= "{$propertyNameAsSnakeTail} AS {$property->getName()},";
        }
        return $columns = rtrim($columns, ',');
    }

}
