<?php
\$host = '127.0.0.1';
\$db   = 'simadok_laknis';
\$user = 'root';
\$pass = '';
\$charset = 'utf8mb4';

\$dsn = "mysql:host=\$host;dbname=\$db;charset=\$charset";
\$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);
     
     // Check if user exists
     \$stmt = \$pdo->prepare("SELECT * FROM users WHERE email = ?");
     \$stmt->execute(['admin@admin.net']);
     \$user_row = \$stmt->fetch();
     
     if (\$user_row) {
         echo "User found: " . \$user_row['name'] . "\n";
         // Update password and active status
         \$new_password = password_hash('admin', PASSWORD_BCRYPT);
         \$stmt = \$pdo->prepare("UPDATE users SET password = ?, is_active = 1, role = 'superadmin' WHERE id = ?");
         \$stmt->execute([\$new_password, \$user_row['id']]);
         echo "User updated successfully with new password hash.\n";
     } else {
         echo "User NOT found. Creating...\n";
         \$new_password = password_hash('admin', PASSWORD_BCRYPT);
         \$stmt = \$pdo->prepare("INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
         \$stmt->execute(['Super Admin', 'admin@admin.net', \$new_password, 'superadmin', 1]);
         echo "User created successfully.\n";
     }
     
} catch (\PDOException \$e) {
     echo "DB Error: " . \$e->getMessage();
}
