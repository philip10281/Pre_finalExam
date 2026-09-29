<?php
// =========================================================
// Database connection settings
// I-adjust ang mga values sa ibaba kung lahi ang setup mo
// (hal. sa XAMPP karaniwan: user = root, password = walang laman)
// =========================================================

$DB_HOST = 'localhost';
$DB_NAME = 'hardware_materials';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed: ' . $e->getMessage());
}
