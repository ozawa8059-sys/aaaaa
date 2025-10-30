<?php
// includes/db_connect.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'digital_shop';
$DB_USER = 'shuju';     // <-- replace with your DB user
$DB_PASS = 'HardRockPass444@#$';     // <-- replace with your DB password
$opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, $opts);
} catch (Exception $e) {
    // In production log this and show a friendly message
    die("Database connection failed: " . $e->getMessage());
}
