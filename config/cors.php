<?php
declare(strict_types=1);

/**
 * Allowlist de orígenes permitidos para CORS con credenciales.
 * Solo entornos de desarrollo local; agregar aquí el dominio real de producción.
 */
return [
    'http://localhost',
    'http://localhost:8000',
    'http://127.0.0.1',
    'http://127.0.0.1:8000',
];
