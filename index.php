<?php

// use-Statement, um später einfacher eine Klasse über den Autoloader zu laden.
use Vapita\Entity\Student;
use Vapita\Model\MysqlModel;

// Composer Autoload Class inkludieren.
include('./vendor/autoload.php');

// Neue Instanz der Datenbank-Klasse mit Verbindungsdaten erstellen. Ändere hier die Angaben entsprechend deiner Datenbank.
$db = new MysqlModel(['host' => 'localhost:8889','dbname' => 'test','charset' =>'utf8'],'root','root');


// Verkürzte Methode zum Abruf von mehreren Datensätzen anhand von Suchparametern.
$studentSingleResultById = $db->find('student',34, Student::class);
$studentResultsByValue = $db->findBy('student',['last_name' => 'Rölke']);
$studentSingleResultByValue = $db->findOneBy('student',['first_name' => 'Anna']);
$studentAll = $db->findAll('student',['last_name' => 'ASC', 'first_name' => 'ASC']);



// Datensätze formatiert ausgeben. Man könnte hier noch mit "if" prüfen, ob überhaupt Datensätze gefunden wurden.
?>
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
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Abrufen</a>
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
                    <h5 class="card-title">Test</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($studentAll as $student) : ?>
                        <li class="list-group-item"><?= $student->first_name . ' ' . $student->last_name ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php

print_r($studentSingleResultById->getId());

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
