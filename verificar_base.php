<?php
/**
 * Script de diagnóstico de conexión a la base de datos cpiset.
 * Uso (CLI):   php verificar_base.php
 * Uso (web):   http://localhost/verificar_base.php
 *
 * Requiere que cpiset.sql esté importado en MySQL/MariaDB.
 */

$host = '127.0.0.1';
$puerto = '3306';
$bd = 'cpiset';
$usuario = 'root';
$password = ''; // XAMPP por defecto sin contraseña; ajústalo si tu MySQL tiene clave.

$tablaEsperada = ['usuarios', 'elecciones', 'actas', 'candidatos', 'partidospoliticos',
                  'provincias', 'distritos', 'regiones', 'mesas'];

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = new PDO("mysql:host=$host;port=$puerto;charset=utf8mb4", $usuario, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "[OK] Conexión a MySQL establecida ($host:$puerto)\n";
} catch (PDOException $e) {
    echo "[ERROR] No se pudo conectar a MySQL en $host:$puerto\n";
    echo "        Motivo: {$e->getMessage()}\n";
    echo "        Verifica que el servicio MySQL esté iniciado en el panel de XAMPP.\n";
    exit(1);
}

// ¿Existe la base de datos?
$existeBd = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$bd'")->fetchColumn();
if (!$existeBd) {
    echo "[ERROR] La base de datos '$bd' NO existe.\n";
    echo "        Impórtala así:\n";
    echo "        mysql -u root -p < cpiset.sql\n";
    echo "        o desde phpMyAdmin: Importar > seleccionar cpiset.sql\n";
    exit(1);
}
echo "[OK] La base de datos '$bd' existe.\n";

$pdo->exec("USE `$bd`");

// Verificar tablas esperadas
$faltan = [];
foreach ($tablaEsperada as $t) {
    $ok = $pdo->query("SHOW TABLES LIKE '$t'")->fetchColumn();
    if (!$ok) $faltan[] = $t;
}
if ($faltan) {
    echo "[ERROR] Faltan tablas: " . implode(', ', $faltan) . "\n";
    echo "        Vuelve a importar cpiset.sql (el script borra y recrea todo).\n";
    exit(1);
}
echo "[OK] Todas las tablas existen.\n";

// Conteos por tabla clave
echo "\nRegistros por tabla:\n";
foreach (['usuarios', 'elecciones', 'candidatos', 'mesas', 'actas', 'distritos'] as $t) {
    $n = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    printf("  %-12s %5d registros%s\n", $t, $n, $n === 0 ? '  <-- ¡VACÍA! vuelve a importar cpiset.sql' : '');
}

echo "\nDiagnóstico completado. Si no hay líneas con ERROR/VACÍA, la base está lista.\n";
