<?php

namespace Vapita\Model;

use PDO;
use PDOStatement;
use stdClass;

interface ModelInterface
{
    /**
     * Stellt sicher, dass alle benötigten Parameter zur Herstellung einer Datenbankverbindung vorhanden sind.
     */
    public function __construct( array $dsn = [], string $username = null, string $password = null, string $options = null, string $type = 'mysql');

    /**
     * @param int $id
     * @param string $entity
     * @return mixed
     *
     * Liefert einen Datensatz anhand des Primary Key.
     */
    public function find(string $entity, int $id);

    /**
     * @param string $entity
     * @param array $sortBy
     * @return mixed
     *
     * Liefert alle Datensätze einer Tabelle.
     */
    public function findAll(string $entity, array $sortBy = []);

    /**
     * @param string $entity
     * @param array $data
     * @param array $sortBy
     * @return mixed
     *
     * Liefert alle Datensätze anhand verschiedener Suchkriterien.
     */
    public function findBy(string $entity, array $data, array $sortBy = []);

    /**
     * @param string $entity
     * @param array $data
     * @return mixed
     *
     * Liefert einen Datensatz anhand verschiedener Suchkriterien.
     */
    public function findOneBy(string $entity, array $data);

    /**
     * @param string $table
     * @param array $data
     * @param int $id
     * @return mixed
     *
     * Fügt neuen Datensatz in angegebene Tabelle ein. Ist $id angegeben,
     * wird der entsprechende Datensatz aktualisiert.
     */
    public function persist(string $table, array $data, int $id);

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return mixed
     *
     * Führt das Datensatz-Update aus.
     */
    public function update(string $table, array $data, array $where);

    /**
     * @param string $table
     * @param array $data
     * @return mixed
     *
     * Führt die Datensatzsicherung aus.
     */
    public function insert(string $table, array $data);

    /**
     * @param string $table
     * @param array $data
     * @param int $limit
     * @return int
     *
     * Der angegebene Datensatz wird entfernt.
     */
    public function delete(string $table, array $data, int $limit = 1):int;

    /**
     * Führt vorbereitete Anweisungen aus.
     */
    public function execute(PDOStatement $statement);
}
