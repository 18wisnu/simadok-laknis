<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;

\$user = User::where('email', 'admin@admin.net')->first();
if (!\$user) {
    \$user = new User();
    \$user->email = 'admin@admin.net';
    \$user->name = 'Super Admin';
}

\$user->password = 'admin'; // Eloquent 'hashed' cast will handle this
\$user->role = 'superadmin';
\$user->is_active = true;
\$user->save();

echo "User updated: " . (\$user->wasRecentlyCreated ? 'Created' : 'Updated') . "\n";
echo "Active: " . (\$user->is_active ? 'Yes' : 'No') . "\n";
echo "Role: " . \$user->role . "\n";
