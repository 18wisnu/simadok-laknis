<?php
// Plain PHP to bypass Laravel hangs/caching
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
     
     // 1. Add is_active column if missing
     $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_active'");
     if (!$stmt->fetch()) {
         $pdo->exec("ALTER TABLE users ADD is_active TINYINT(1) DEFAULT 0 AFTER role");
         echo "Column 'is_active' added.<br>";
     } else {
         echo "Column 'is_active' already exists.<br>";
     }

     // 2. Fix admin user
     $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
     $stmt->execute(['admin@admin.net']);
     $row = $stmt->fetch();
     
     $password_hash = password_hash('admin', PASSWORD_BCRYPT);
     
     if ($row) {
         $stmt = $pdo->prepare("UPDATE users SET password = ?, is_active = 1, role = 'superadmin' WHERE id = ?");
         $stmt->execute([$password_hash, $row['id']]);
         echo "Admin updated successfully (Password reset to 'admin').<br>";
     } else {
         $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
         $stmt->execute(['Super Admin', 'admin@admin.net', $password_hash, 'superadmin', 1]);
         echo "Admin created successfully.<br>";
     }
     
} catch (\PDOException $e) {
     echo "DATABASE ERROR: " . $e->getMessage();
}
?>
