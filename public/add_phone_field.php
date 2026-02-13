<?php
$db_host = '127.0.0.1';
$db_name = 'simadok_laknis';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone_number'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD phone_number VARCHAR(20) NULL AFTER email");
        echo "Column 'phone_number' added.<br>";
    } else {
        echo "Column 'phone_number' already exists.<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
