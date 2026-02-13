<?php
// Bypassing cache with unique filename
$db_host = '127.0.0.1';
$db_name = 'simadok_laknis';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Column check
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_active'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE users ADD is_active TINYINT(1) DEFAULT 0 AFTER role");
        echo "Column 'is_active' added.<br>";
    }

    // 2. Admin fix
    $hash = password_hash('admin', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@admin.net']);
    if ($row = $stmt->fetch()) {
        $pdo->prepare("UPDATE users SET password = ?, is_active = 1, role = 'superadmin' WHERE id = ?")
            ->execute([$hash, $row['id']]);
        echo "Admin updated.<br>";
    } else {
        $pdo->prepare("INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())")
            ->execute(['Super Admin', 'admin@admin.net', $hash, 'superadmin', 1]);
        echo "Admin created.<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
