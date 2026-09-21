<?php
declare(strict_types=1);

/**
 * Clase Database - Patrón Singleton para conexión PDO
 * Gestiona una única instancia de conexión reutilizable
 */
class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /** Evita instanciación externa */
    private function __construct() {}
    private function __clone() {}

    /**
     * Obtiene la instancia única de PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$config = require __DIR__ . '/../config/database.php';
            $cfg = self::$config;

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $cfg['host'],
                $cfg['port'],
                $cfg['dbname'],
                $cfg['charset']
            );

            try {
                self::$instance = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
            } catch (PDOException $e) {
                error_log('Error de conexión BD: ' . $e->getMessage());
                throw new RuntimeException('No se pudo conectar a la base de datos.');
            }
        }
        return self::$instance;
    }

    /** Cierra la conexión (útil en tests) */
    public static function close(): void
    {
        self::$instance = null;
    }
}