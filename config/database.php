
<?php

date_default_timezone_set('Europe/Tallinn');

function getDatabase(): PDO
{
    $databasePath = __DIR__ . '/../database/database.sqlite';

    $pdo = new PDO('sqlite:' . $databasePath);

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    return $pdo;
}
