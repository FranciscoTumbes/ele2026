<?php
declare(strict_types=1);
require_once __DIR__ . '/core/AppException.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/models/Ubicacion.php';
require_once __DIR__ . '/models/Ubicacion/Provincia.php';
require_once __DIR__ . '/models/Ubicacion/Distrito.php';
require_once __DIR__ . '/models/Ubicacion/CentroVotacion.php';
require_once __DIR__ . '/models/Eleccion.php';
require_once __DIR__ . '/models/AgrupacionPolitica.php';
require_once __DIR__ . '/models/Candidato.php';
require_once __DIR__ . '/models/Elector.php';
require_once __DIR__ . '/models/ActaSufragio.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/repositories/ResultadosRepository.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // === 1. AUTENTICACIÓN ===
    $usuarioModel = new Usuario();
    $user = $usuarioModel->autenticar('admin', 'admin123');

    if (!$user) {
        throw new AppException('Credenciales inválidas');
    }
    echo "✅ Bienvenido {$user['nombres']} ({$user['rol']})\n";

    // === 2. LISTAR PROVINCIAS DE TUMBES ===
    $provinciaModel = new Provincia();
    $provincias = $provinciaModel->porRegion(1);
    echo "\n📍 Provincias de Tumbes:\n";
    foreach ($provincias as $p) {
        echo "  - {$p['nombre']} ({$p['codigo']})\n";
    }

    // === 3. LISTAR CANDIDATOS A GOBERNADOR ===
    $candidatoModel = new Candidato();
    $candidatos = $candidatoModel->porEleccionYCargo(1, 1);
    echo "\n🗳️ Candidatos a Gobernador Regional:\n";
    foreach ($candidatos as $c) {
        echo "  [{$c['siglas']}] {$c['nombres']} {$c['apellido_paterno']}\n";
    }

    // === 4. REGISTRAR UN ACTA (ejemplo) ===
    $actaModel = new ActaSufragio();
    $actaData = [
        'mesa_id'              => 1,
        'eleccion_id'          => 1,
        'electores_habilitados'=> 300,
        'votos_validos'        => 250,
        'votos_blancos'        => 10,
        'votos_nulos'          => 5,
        'votos_impugnados'     => 2,
        'total_votantes'       => 267,
        'observaciones'        => 'Sin observaciones'
    ];

    $detalles = [
        ['candidato_id' => 1, 'votos_obtenidos' => 150],
        ['candidato_id' => 2, 'votos_obtenidos' => 100],
    ];

    $actaId = $actaModel->registrarActa($actaData, $detalles, (int)$user['id']);
    echo "\n📋 Acta registrada con ID: {$actaId}\n";

    // === 5. RESULTADOS ===
    $resultados = new ResultadosRepository();
    $total = $resultados->totalizacionCargo(1, 1);
    echo "\n📊 Totalización Gobernador Regional:\n";
    foreach ($total['candidatos'] as $c) {
        echo "  {$c['candidato']} ({$c['siglas']}): {$c['total_votos']} votos ({$c['porcentaje']}%)\n";
    }

} catch (AppException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage(), 'context' => $e->getContext()]);
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Error interno del sistema']);
}