<?php
declare(strict_types=1);

/**
 * Helpers globales para vistas.
 */

/** Escapado HTML obligatorio para cualquier salida dinámica (anti-XSS). */
function e(null|string|int|float $value = ''): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
