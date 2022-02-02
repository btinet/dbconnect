<?php

namespace Vapita\Model;

use PDO;
use PDOStatement;

interface ModelInterface
{
    /**
     * Stellt sicher, dass alle benötigten Parameter zur Herstellung einer Datenbankverbindung vorhanden sind.
     */
    public function __construct( array $dsn = [], string $username = null, string $password = null, string $options = null, string $type = 'mysql');

    /**
     * Bereitet Anweisungen für den Abruf von Datensätzen vor.
     */
    public function select(string $preparedStatement, array $data = [], string $mode = null);

    public function find(string $table, $id, $mode = null);

    public function findAll(string $table, array $sortBy = [], $mode = PDO::FETCH_OBJ);

    public function findBy(string $table, array $data, array $sortBy = [], $mode = PDO::FETCH_OBJ);

    public function findOneBy(string $table, array $data, $mode = null);

    public function persist(string $table, array $data, int $id);
    public function update(string $table, array $data, array $where);
    public function insert(string $table, array $data);
    public function delete(string $table, array $data, int $limit = 1):int;

    /**
     * Führt vorbereitete Anweisungen aus.
     */
    public function execute(PDOStatement $statement);
}