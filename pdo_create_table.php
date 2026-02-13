<?php
$host = '127.0.0.1';
$db   = 'simadok_laknis';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $sql = "CREATE TABLE IF NOT EXISTS audit_logs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NULL,
        action VARCHAR(255) NOT NULL,
        model_type VARCHAR(255) NOT NULL,
        model_id BIGINT UNSIGNED NOT NULL,
        old_values JSON NULL,
        new_values JSON NULL,
        description TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
     $pdo->exec($sql);
     echo "TABLE_CREATED_SUCCESSFULLY";
} catch (\PDOException $e) {
     echo "ERROR: " . $e->getMessage();
}
