<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dsn = sprintf(
    "mysql:host=%s;dbname=%s;charset=utf8mb4",
    $_ENV['db_host'],
    $_ENV['db_name']
);

try {
    $connection = new PDO(
        $dsn,
        $_ENV['db_user'],
        $_ENV['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo "Connected!!!!";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
