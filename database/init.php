<?php


require_once __DIR__ . '/../config/database.php';

$pdo = getDatabase();

$sql = file_get_contents(__DIR__ . '/schema.sql');

$pdo->exec($sql);

echo "Database initialized successfully!";