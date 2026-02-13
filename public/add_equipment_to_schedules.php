<?php
$db_host = '127.0.0.1';
$db_name = 'simadok_laknis';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW COLUMNS FROM schedules LIKE 'equipment_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE schedules ADD equipment_id BIGINT UNSIGNED NULL AFTER ends_at");
        $pdo->exec("ALTER TABLE schedules ADD CONSTRAINT fk_schedule_equipment FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL");
        echo "Column 'equipment_id' and foreign key added to schedules.<br>";
    } else {
        echo "Column 'equipment_id' already exists.<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
