<?php
declare(strict_types=1);

/**
 * View - Localizador seguro de vistas.
 * Usa una WHITELIST explícita: solo se pueden renderizar las vistas registradas,
 * lo que elimina cualquier riesgo de inclusión remota/local de archivos (LFI).
 */
class View
{
    /** @var array<string,string> Mapa nombre => ruta absoluta del archivo de vista */
    private static array $views = [
        'layout'      => __DIR__ . '/../views/layout.php',
        'login'       => __DIR__ . '/../views/login.php',
        'resultados'  => __DIR__ . '/../views/resultados.php',
        'digitacion'  => __DIR__ . '/../views/digitacion.php',
    ];

    public static function exists(string $name): bool
    {
        return isset(self::$views[$name]);
    }

    /**
     * Resuelve el nombre de vista contra la whitelist.
     * @throws AppException si la vista no está registrada.
     */
    public static function path(string $name): string
    {
        if (!self::exists($name)) {
            throw new AppException("Vista no permitida o inexistente: {$name}");
        }
        return self::$views[$name];
    }

    /** Renderiza una vista con datos (escapa variables accesibles vía e()). */
    public static function render(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require self::path($name);
    }
}
