<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

$u = User::firstOrCreate(
    ['email' => 'admin@wflf.com'],
    ['name' => 'Admin WFLF', 'password' => Hash::make('password123'), 'is_admin' => true]
);
echo "Email: admin@wflf.com\nPassword: password123\n";
