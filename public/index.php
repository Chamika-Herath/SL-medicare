<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Load Composer Autoloader
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    // If running in development without composer install yet, render a warning
    die("<h3>Composer Dependencies Missing</h3><p>Please run <code>composer install</code> or start the virtualized container environment via <code>docker-compose up --build</code> to install requirements.</p>");
}

// Bootstrap Laravel Application
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
