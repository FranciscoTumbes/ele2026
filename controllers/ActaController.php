<?php
declare(strict_types=1);

class ActaController extends Controller
{
    /**
     * POST /api/actas
     * Registra un acta completa con validación de consistencia
     */
    public function registrar(Request $request): void
    {
        $user = $this->requireAuth();
        $data = $request->input();

        // Validación de cabecera
        $this->validate($data, [
            'mesa_id'               => 'required|numeric',
            'eleccion_id'           => 'required|numeric',
            'electores_habilitados' => 'required|numeric',
            'votos_validos'         => 'required|numeric',
            'votos_blancos'         => 'required|numeric',
            'votos_nulos'           => 'required|numeric',
            'total_votantes'        => 'required|numeric'
        ]);

        // Validación de detalles
        if (empty($data['detalles']) || !is_array($data['detalles'])) {
            Response::error('Debe incluir el detalle de votos por candidato', 422);
        }

        foreach ($data['detalles'] as $i => $d) {
            if (empty($d['candidato_id']) || !isset($d['votos_obtenidos'])) {
                Response::error("Detalle #{$i} incompleto", 422);
            }
        }

        try {
            $actaModel = new ActaSufragio();
            $actaId = $actaModel->registrarActa(
                [
                    'mesa_id'               => (int)$data['mesa_id'],
                    'eleccion_id'           => (int)$data['eleccion_id'],
                    'electores_habilitados' => (int)$data['electores_habilitados'],
                    'votos_validos'         => (int)$data['votos_validos'],
                    'votos_blancos'         => (int)$data['votos_blancos'],
                    'votos_nulos'           => (int)$data['votos_nulos'],
                    'votos_impugnados'      => (int)($data['votos_impugnados'] ?? 0),
                    'total_votantes'        => (int)$data['total_votantes'],
                    'observaciones'         => $data['observaciones'] ?? null
                ],
                $data['detalles'],
                (int)$user['id']
            );

            Response::success(['acta_id' => $actaId], 'Acta registrada correctamente', 201);

        } catch (AppException $e) {
            Response::error($e->getMessage(), 422);
        }
    }

    /** GET /api/actas/pendientes?centro_id=X&eleccion_id=Y */
    public function pendientes(Request $request): void
    {
        $centroId   = (int)$request->input('centro_id');
        $eleccionId = (int)$request->input('eleccion_id');

        if (!$centroId || !$eleccionId) {
            Response::error('Los parámetros centro_id y eleccion_id son requeridos', 400);
        }

        $db = Database::getInstance();
        // Nota: el filtro por eleccion_id va en el ON del LEFT JOIN; si se pusiera
        // en el WHERE, las mesas con acta de otra elección quedarían excluidas
        // erróneamente al no tener coincidencia (a.* sería NULL).
        $sql = "SELECT m.id, m.numero_mesa, m.electores_habilitados,
                       a.estado AS estado_acta
                FROM mesas_sufragio m
                LEFT JOIN actas_sufragio a
                    ON a.mesa_id = m.id AND a.eleccion_id = :eid
                WHERE m.centro_id = :cid
                  AND (a.id IS NULL OR a.estado = 'PENDIENTE')
                ORDER BY m.numero_mesa";
        $stmt = $db->prepare($sql);
        $stmt->execute([':cid' => $centroId, ':eid' => $eleccionId]);

        Response::success($stmt->fetchAll());
    }

    /** PUT /api/actas/{id}/verificar */
    public function verificar(Request $request): void
    {
        $user = $this->requireAuth();
        $actaId = (int)$request->param('id');

        $model = new ActaSufragio();
        $acta = $model->find($actaId);
        if (!$acta) Response::error('Acta no encontrada', 404);
        if ($acta['estado'] !== 'DIGITADA') {
            Response::error('El acta no está en estado DIGITADA', 422);
        }

        $model->verificar($actaId, (int)$user['id']);
        Response::success(null, 'Acta verificada');
    }
}