<?php
// Update these to match your environment
$DB_HOST = 'db';
$DB_NAME = 'dirt';
$DB_USER = 'dirtuser';
$DB_PASS = 'update_password';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    die('DB Connection failed: ' . $e->getMessage());
}
?>