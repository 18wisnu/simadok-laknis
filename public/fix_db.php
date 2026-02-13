<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<h2>Fixing Database Table</h2>";

try {
    if (!Schema::hasTable('audit_logs')) {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        echo "<p style='color: green;'>SUCCESS: Table 'audit_logs' created!</p>";
    } else {
        echo "<p style='color: blue;'>NOTE: Table 'audit_logs' already exists.</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/audit-logs'>Back to Audit Logs</a></p>";
