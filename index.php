<?php

// use-Statement, um später einfacher eine Klasse über den Autoloader zu laden.
use Vapita\Model\Database;

// Composer Autoload Class inkludieren.
include('./vendor/autoload.php');

// Neue Instanz der Datenbank-Klasse mit Verbindungsdaten erstellen. Ändere hier die Angaben entsprechend deiner Datenbank.
$db = new Database(['host' => 'localhost:8889','dbname' => 'information_schema','charset' =>'utf8'],'root','root');

// Datensätze aus einer Tabelle abfragen. Hier wurde nach Datensätzen in der Tabelle "plugins" gesucht, die "binlog" im Feld "plugin_name" enthalten.
$plugins = $db->select("SELECT * FROM plugins WHERE plugin_name = :plugin_name",['plugin_name' => 'binlog']);

// Verkürzte Methode zum Abruf von mehreren Datensätzen anhand von Suchparametern.
$pluginResults = $db->findBy('plugins',[
    'plugin_version' => '1.0'
]);

// Verkürzte Methode zum Abruf eines Datensatzes anhand von Suchparametern.
$pluginResult = $db->findOneBy('plugins',[
    'plugin_name' => 'binlog'
]);

// Datensätze formatiert ausgeben. Man könnte hier noch mit "if" prüfen, ob überhaupt Datensätze gefunden wurden.
print('<h2>Benutzerdefinierte Abfrage</h2>');
print('<pre><code>');
print_r($plugins->fetchAll(3));
print('</code></pre>');

print('<h2>Mehrere Datensätze (findBy())</h2>');
print('<pre><code>');
print_r($pluginResults);
print('</code></pre>');

print('<h2>Ein Datensatz (findOneBy())</h2>');
print('<pre><code>');
print_r($pluginResult);
print('</code></pre>');
