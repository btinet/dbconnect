<?php

use Vapita\Entity\Student;
use Vapita\Model\MysqlModel;

/**
 * Composer Hilfsklasse laden.
 */
include('./vendor/autoload.php');

/**
 * Definiere Ergebnisvariablen für die Datenbankaktionen. Sind diese nicht 'false', wird an späterer
 * Stelle eine Information ausgegeben, die auf das Ergebnis der getätigten Aktion hinweist.
 */
$resultInsert = false;
$resultUpdate = false;
$resultDelete = false;

/**
 * Container für die Datensätze definieren.
 */
$student = false;

/**
 * Neue Instanz der Datenbank-Klasse mit Verbindungsdaten erstellen.
 * Ändere hier die Angaben entsprechend deiner Datenbank.
 */
$db = new MysqlModel(
        [
        'host' => 'localhost:3306',
        'dbname' => 'test',
        'charset' =>'utf8'
        ],
        'root',
        'root'
);

// Datenbankaktionen auslösen

/**
 * Ist der Schlüssel "delete" im $_POST-Array vorhanden, führe Methode zum Löschen
 * eines Datensatzes anhand gegebener Id aus.
 */
if(key_exists('delete',$_POST)){
    $resultDelete = $db->delete('student',['id' => $_POST['delete']]);
}

/**
 * Ist der Schlüssel "persist" im $_POST-Array vorhanden, führe Methode zum Erstellen
 * eines Datensatzes anhand gegebener Werte aus.
 */
if(key_exists('persist',$_POST)){
    $resultInsert = $db->persist('student',[
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
    ]);
}

/**
 * Ist der Schlüssel "update" im $_POST-Array vorhanden, führe Methode zum Aktualisieren
 * eines Datensatzes anhand gegebener Werte aus.
 */
if(key_exists('update',$_POST)){
    $resultUpdate = $db->persist('student',[
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
    ],
    $_POST['id']);
}

/**
 * Datensatz von StudentIn anhand per GET übergebener Id abrufen und in Variable speichern.
 */
if(key_exists('student_id',$_GET)){
    $student = $db->find(Student::class,$_GET['student_id']);
}

/**
 * Alle Datensätze der Tabelle 'student' der Variable $students als Array zuweisen.
 * Datensätze zuerst nach Nachname, dann nach Vorname sortieren (jeweils aufsteigend).
 */
$students = $db->findAll(Student::class,['last_name' => 'ASC', 'first_name' => 'ASC']);
$studentsWagner = $db->findOneBy(Student::class,[
        'lastName' => 'Wagner',
]);

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
    <link href="./css/style.css" rel="stylesheet">

    <title>Datenbank Tutorial</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="http://<?= $_SERVER['HTTP_HOST'] ?>">Datenbank-Tutorial</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="http://<?= $_SERVER['HTTP_HOST'] ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" target="_blank" href="https://www.vapita.de/develop">Vapita Developer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" target="_blank" href="https://github.com/btinet/dbconnect">Docs</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-3">
    <div class="row g-3">
        <div class="col-12 col-lg-5 order-first order-lg-last">
            <div class="card" style="border: 1px solid  var(--bs-gray-300)">
                <div class="card-header">
                    StudentIn bearbeiten
                </div>
                <div class="card-body bg-light">
                    <h5 class="card-title"><?= $student ??  'Einzelansicht' ?></h5>
                    <!-- Beginn, Formular zum Speichern eines Datensatzes -->
                    <?php if($student): ?>
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="first_name">Vorname</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" value="<?= $student->getFirstName() ?? '' ?>" placeholder="Vorname" aria-label="First name" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="last_name">Nachname</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" value="<?= $student->getLastName() ?? '' ?>" placeholder="Nachname" aria-label="Last name" required>
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="id" value="<?= $student->getId() ?>">
                                <button class="btn btn-primary w-100" type="submit" name="update">Aktualisieren</button>
                            </div>
                        </div>
                    </form>
                    <?php endif ?>
                    <!-- Ende, Formular -->
                </div>
                <div class="card-footer">
                    <?php if(false !== $resultUpdate): ?>
                    <div class="bg-success-light text-black p-1 text-center">
                        <?= $resultUpdate ? 'Datensatz aktualisiert' : 'Datensatz unverändert' ?>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="card bg-light" style="border: 1px solid  var(--bs-gray-300)">
                <div class="card-header">
                    Alle StudentInnen
                </div>
                <div class="card-body">
                    <h5 class="card-title">Übersicht</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Weitere StudentInnen hinzufügen</h6>
                    <!-- Beginn, Formular zum Speichern eines Datensatzes -->
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-12 col-lg-4">
                                <input type="text" name="first_name" class="form-control" placeholder="Vorname" aria-label="First name" required>
                            </div>
                            <div class="col-12 col-lg-4">
                                <input type="text" name="last_name" class="form-control" placeholder="Nachname" aria-label="Last name" required>
                            </div>
                            <div class="col-12 col-lg-4">
                                <button class="btn btn-success w-100" type="submit" name="persist">Speichern</button>
                            </div>
                        </div>
                    </form>
                    <!-- Ende, Formular -->
                </div>
                <div class="list-group list-group-flush">
                    <span class="list-group-item">StudentInnen</span>
                    <!-- Für jeden Datensatz einen Listeneintrag erstellen -->
                    <?php foreach ($students as $student) : ?>
                        <a href="<?= "{$_SERVER['REMOTE_HOST']}?student_id={$student->getId()}" ?>" class=" <?= $student->getId() != $_GET['student_id'] ?: 'active' ?> list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                               <?= $student?>
                            </span>
                            <!-- Beginn, Formular zum Löschen des Datensatzes -->
                            <form method="post">
                                <button class="btn btn-sm btn-danger" type="submit" name="delete" value="<?= $student->getId() ?>">Löschen</button>
                            </form>
                            <!-- Ende, Formular -->
                        </a>
                    <?php endforeach; ?>
                    <!-- Ende, Listeneintrag -->
                </div>
                <div class="card-footer">
                    <?php if(false !== $resultInsert): ?>
                        <div class="bg-success-light text-black p-1 text-center">
                            <?= $resultInsert ? 'Datensatz erstellt' : 'Datensatz unverändert' ?>
                        </div>
                    <?php endif ?>
                    <?php if(false !== $resultDelete): ?>
                        <div class="bg-danger-light text-black p-1 text-center">
                            <?= $resultInsert ? 'Datensatz nicht gelöscht' : 'Datensatz gelöscht' ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
