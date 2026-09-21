<?php
declare(strict_types=1);

// === 1. Manejo de errores global ===
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function(Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        Response::error('Error interno del sistema', 500);
    }
});

// === 2. Autoload simple ===
$dirs = ['core', 'models', 'controllers', 'middleware', 'repositories'];
foreach ($dirs as $dir) {
    foreach (glob(__DIR__ . "/../{$dir}/*.php") as $file) {
        require_once $file;
    }
}

// === 3. CORS para peticiones AJAX ===
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// === 4. Inicia sesión ===
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === 5. Construye request y router ===
$request = new Request();
$router  = new Router();

// === 6. Carga rutas ===
require __DIR__ . '/../routes.php';

// === 7. Despacha ===
try {
    $router->dispatch($request);
} catch (AppException $e) {
    Response::error($e->getMessage(), 400, $e->getContext());
} catch (Throwable $e) {
    Response::error('Error interno: ' . $e->getMessage(), 500);
}