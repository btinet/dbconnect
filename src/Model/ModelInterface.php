<?php

namespace Vapita\Model;

use PDO;
use PDOStatement;
use stdClass;

interface ModelInterface
{
    /**
     * Stellt sicher, dass alle benötigten Parameter zur Herstellungen einer Datenbankverbindung vorhanden sind.
     */
    public function __construct( array $dsn = [], string $username = null, string $password = null, string $options = null, string $type = 'mysql');

    /**
     * Bereitet Anweisungen für den Abruf von Datensätzen vor.
     */
    public function select(string $preparedStatement, array $data = [], string $mode = null);

    public function findBy(string $table, array $data, $mode = PDO::FETCH_OBJ):array;

    public function findOneBy(string $table, array $data, $mode = null):stdClass;

    /**
     * Führt vorbereitete Anweisungen aus.
     */
    public function execute(PDOStatement $statement);
}