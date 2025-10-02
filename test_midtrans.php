<?php
require_once 'vendor/autoload.php';

use Midtrans\Config;

echo "Midtrans Config class loaded successfully\n";
echo "Server key: " . Config::$serverKey . "\n";