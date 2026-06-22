<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$i = App\Models\Internship::where('title', 'like', '%Video Editor%')->first();
if (!$i) {
    $i = App\Models\Internship::first();
}

echo "Description:\n";
echo var_export($i->description, true) . "\n\n";

echo "Requirements:\n";
echo var_export($i->requirements, true) . "\n\n";

echo "Benefits:\n";
echo var_export($i->benefits, true) . "\n\n";
