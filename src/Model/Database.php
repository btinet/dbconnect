<?php

namespace Vapita\Model {

    use DateTime;
    use Exception;
    use PDO;
    use PDOException;
    use PDOStatement;
    use stdClass;

    class Database extends PDO implements ModelInterface
    {
        public string $dsnString;

        public function __construct(array $dsn = [], string $username = null, string $password = null, string $options = null, string $type = 'mysql')
        {
            try {
                $this->dsnString = "{$type}:";
                $i = 0;
                foreach ($dsn as $key => $value)
                {
                    $this->dsnString .= "{$key}={$value}";
                    if (++$i !== count($dsn)) $this->dsnString .= ";";
                }

                parent::__construct($this->dsnString, $username, $password);
                $this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_BOTH);

            } catch (PDOException $e){
                echo 'Exception abgefangen: '.$e->getMessage()."\n";
            }

        }

        public function select(string $preparedStatement, array $data = [], $mode = null)
        {
            $statement = $this->prepare($preparedStatement);
            foreach ($data as $key => $value){
                switch ($value)
                {
                    case $value instanceof DateTime:
                        echo("DateTime \n");
                        break;
                    case is_string($value):
                        echo("String \n");
                        break;
                    case is_numeric($value):
                        $value = strval($value);
                        echo("Zahl {$value} \n");
                        break;
                }
                $statement->bindValue(':'.$key, $value);
            }
            $this->execute($statement);
            return $statement;
        }

        public function findBy(string $table, array $data, $mode = PDO::FETCH_OBJ): array
        {
            $preparedStatement = "";
            $i = 0;
            $dataLength = count($data);
            foreach ($data as $field => $value){
                $preparedStatement .= " {$field} = :{$field} ";
                if (++$i !== $dataLength) $preparedStatement .= " AND ";
            }
            $result =  self::select("SELECT * FROM {$table} WHERE ({$preparedStatement})",$data);
            return $result->fetchAll($mode);
        }

        public function findOneBy(string $table, array $data, $mode = null): stdClass
        {
            $preparedStatement = "";
            $i = 0;
            $dataLength = count($data);
            foreach ($data as $field => $value){
                $preparedStatement .= " {$field} = :{$field} ";
                if (++$i !== $dataLength) $preparedStatement .= " AND ";
            }
            $result =  self::select("SELECT * FROM {$table} WHERE ({$preparedStatement}) LIMIT 0,1",$data, $mode);

                if (false === $object = $result->fetchObject($mode)) return new stdClass();
                return $object;
        }

        public function execute(PDOStatement $statement): bool
        {
            return $statement->execute();
        }
    }
}