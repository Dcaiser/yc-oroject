<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Illuminate\Support\Facades\DB::table('users')
    ->where('email', 'admin@example.com')
    ->update([
        'password' => Illuminate\Support\Facades\Hash::make('admin1234'),
        'email_verified_at' => now(),
    ]);

echo "Admin password reset to admin1234\n";
