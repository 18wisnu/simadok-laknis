<?php
$db_host = '127.0.0.1';
$db_name = 'simadok_laknis';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Make starts_at and ends_at nullable
    $pdo->exec("ALTER TABLE schedules MODIFY starts_at TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE schedules MODIFY ends_at TIMESTAMP NULL");
    
    echo "Columns 'starts_at' and 'ends_at' are now NULLABLE.<br>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
