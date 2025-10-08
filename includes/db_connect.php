<?php
// includes/db_connect.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'gallery_db';
$DB_USER = 'root';
$DB_PASS = ''; // set your mysql root password if any

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
