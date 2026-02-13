<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\User;

\$users = User::all();
\$output = "Total Users: " . \$users->count() . "\n\n";

foreach (\$users as \$user) {
    \$output .= "ID: {\$user->id}\n";
    \$output .= "Name: {\$user->name}\n";
    \$output .= "Email: {\$user->email}\n";
    \$output .= "Role: {\$user->role}\n";
    \$output .= "Active: " . (\$user->is_active ? 'Yes' : 'No') . "\n";
    \$output .= "Password Hash: " . substr(\$user->password, 0, 10) . "...\n";
    \$output .= "-------------------\n";
}

file_put_contents('user_list.txt', \$output);
echo "Done";
