<?php

use Vapita\Model\MysqlModel;

/**
 * Composer Hilfsklasse laden.
 */
include('./vendor/autoload.php');

/**
 * Neue Instanz der Datenbank-Klasse mit Verbindungsdaten erstellen.
 * Ändere hier die Angaben entsprechend deiner Datenbank.
 */
$db = new MysqlModel(
        [
        'host' => 'localhost:8889',
        'dbname' => 'test',
        'charset' =>'utf8'
        ],
        'root',
        'root'
);

/**
 * Ist der Schlüssel 'delete' im $_POST-Array vorhanden, führe Methode zum Löschen
 * eines Datensatzes anhand gegebener Id aus.
 */
if(key_exists('delete',$_POST)){
    $db->delete('student',['id' => $_POST['delete']]);
}

/**
 * Ist der Schlüssel 'persist' im $_POST-Array vorhanden, führe Methode zum Erstellen
 * eines Datensatzes anhand gegebener Werte aus.
 */
if(key_exists('persist',$_POST)){
    $db->persist('student',[
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
    ]);
}

/**
 * Alle Datensätze der Tabelle 'student' der Variable $students als Array zuweisen.
 * Datensätze zuerst nach Nachname, dann nach Vorname sortieren (jeweils aufsteigend).
 */
$students = $db->findAll('student',['last_name' => 'ASC', 'first_name' => 'ASC']);

?>

<!-- Beginn, HTML Dokument -->
<!doctype html>
<html lang="de">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Datenbank Tutorial</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Datenbanken</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= $_SERVER['PHP_SELF'] ?>">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-3">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Alle StudentInnen
                </div>
                <div class="card-body">
                    <h5 class="card-title">Übersicht</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Weitere StudentInnen hinzufügen</h6>
                    <!-- Beginn, Formular zum Speichern eines Datensatzes -->
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-12 col-lg-5">
                                <input type="text" name="first_name" class="form-control" placeholder="Vorname" aria-label="First name" required>
                            </div>
                            <div class="col-12 col-lg-5">
                                <input type="text" name="last_name" class="form-control" placeholder="Nachname" aria-label="Last name" required>
                            </div>
                            <div class="col-12 col-lg-2">
                                <button class="btn btn-primary w-100" type="submit" name="persist">Speichern</button>
                            </div>
                        </div>
                    </form>
                    <!-- Ende, Formular -->
                </div>
                <ul class="list-group list-group-flush">
                    <!-- Für jeden Datensatz einen Listeneintrag erstellen -->
                    <?php foreach ($students as $student) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                               <?= $student->first_name . ' ' . $student->last_name ?>
                            </span>
                            <!-- Beginn, Formular zum Löschen des Datensatzes -->
                            <form method="post">
                                <button class="btn btn-sm btn-danger" type="submit" name="delete" value="<?= $student->id ?>">Löschen</button>
                            </form>
                            <!-- Ende, Formular -->
                        </li>
                    <?php endforeach; ?>
                    <!-- Ende, Listeneintrag -->
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
