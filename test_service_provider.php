<?php
require_once 'vendor/autoload.php';

// Try to instantiate the MidtransServiceProvider directly
try {
    $app = new Illuminate\Foundation\Application(__DIR__);
    $provider = new App\Providers\MidtransServiceProvider($app);
    echo "MidtransServiceProvider instantiated successfully\n";
    
    // Try to call the boot method
    $provider->boot();
    echo "MidtransServiceProvider boot method executed successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}