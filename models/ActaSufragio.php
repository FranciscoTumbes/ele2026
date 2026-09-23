<?php
declare(strict_types=1);

class ActaSufragio extends Model
{
    protected string $table = 'actas_sufragio';

    /**
     * Registra un acta completa con sus detalles en una transacción
     *
     * @param array $acta     Datos del acta (cabecera)
     * @param array $detalles Array de ['candidato_id' => X, 'votos_obtenidos' => Y]
     * @param int   $usuarioId ID del digitador
     * @return int ID del acta creada
     */
    public function registrarActa(array $acta, array $detalles, int $usuarioId): int
    {
        $this->validarConsistencia($acta, $detalles);

        try {
            $this->db->beginTransaction();

            // 1. Insertar cabecera del acta
            $acta['digitado_por'] = $usuarioId;
            $acta['estado'] = 'DIGITADA';
            $acta['fecha_digitacion'] = date('Y-m-d H:i:s');
            $actaId = $this->create($acta);

            // 2. Insertar detalles por candidato
            $sqlDetalle = "INSERT INTO detalle_acta_candidato
                           (acta_id, candidato_id, votos_obtenidos)
                           VALUES (:aid, :cid, :votos)";
            $stmt = $this->db->prepare($sqlDetalle);

            foreach ($detalles as $d) {
                $stmt->execute([
                    ':aid'   => $actaId,
                    ':cid'   => $d['candidato_id'],
                    ':votos' => (int) $d['votos_obtenidos']
                ]);
            }

            // 3. Auditoría
            $this->registrarAuditoria($usuarioId, 'INSERT', 'actas_sufragio', $actaId);

            $this->db->commit();
            return $actaId;

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log('Error al registrar acta: ' . $e->getMessage());
            throw new AppException('No se pudo registrar el acta', [], 0, $e);
        }
    }

    /**
     * Valida que la suma de votos coincida con el total de votantes
     */
    private function validarConsistencia(array $acta, array $detalles): void
    {
        // 1. Validación referencial: mesa y elección deben existir y estar vigentes
        $mesa = $this->queryOne(
            "SELECT id, estado FROM mesas_sufragio WHERE id = :id",
            [':id' => (int)($acta['mesa_id'] ?? 0)]
        );
        if (!$mesa) {
            throw new AppException('La mesa indicada no existe');
        }
        if ($mesa['estado'] !== 'ACTIVA') {
            throw new AppException("La mesa no está activa (estado: {$mesa['estado']})");
        }

        $eleccion = $this->queryOne(
            "SELECT id, estado FROM elecciones WHERE id = :id",
            [':id' => (int)($acta['eleccion_id'] ?? 0)]
        );
        if (!$eleccion) {
            throw new AppException('La elección indicada no existe');
        }
        if (!in_array($eleccion['estado'], ['CONVOCADA', 'EN_CURSO'], true)) {
            throw new AppException("No se puede digitar actas para una elección en estado {$eleccion['estado']}");
        }

        // 2. No duplicados: constraint UNIQUE(mesa_id, eleccion_id) + chequeo explícito
        $existente = $this->queryOne(
            "SELECT id, estado FROM actas_sufragio WHERE mesa_id = :mid AND eleccion_id = :eid",
            [':mid' => (int)$acta['mesa_id'], ':eid' => (int)$acta['eleccion_id']]
        );
        if ($existente) {
            throw new AppException("Ya existe un acta ({$existente['estado']}) para esta mesa y elección");
        }

        // 3. Valores numéricos no negativos y coherencia de totales parciales
        foreach (['electores_habilitados','votos_validos','votos_blancos','votos_nulos','votos_impugnados','total_votantes'] as $campo) {
            if (!isset($acta[$campo]) || !is_numeric($acta[$campo]) || (int)$acta[$campo] < 0) {
                throw new AppException("El campo '{$campo}' debe ser un entero >= 0");
            }
        }
        if ((int)$acta['total_votantes'] > (int)$acta['electores_habilitados']) {
            throw new AppException('El total de votantes supera los electores habilitados');
        }

        // 4. Validación de detalles: formato, votos no negativos, candidatos sin duplicar
        $sumaDetalles = 0;
        $idsVistos = [];
        foreach ($detalles as $i => $d) {
            if (empty($d['candidato_id']) || !isset($d['votos_obtenidos'])) {
                throw new AppException("Detalle #{$i} incompleto");
            }
            if (!is_numeric($d['votos_obtenidos']) || (int)$d['votos_obtenidos'] < 0) {
                throw new AppException("Los votos del detalle #{$i} deben ser enteros >= 0");
            }
            $cid = (int)$d['candidato_id'];
            if (in_array($cid, $idsVistos, true)) {
                throw new AppException("Candidato {$cid} repetido en el detalle del acta");
            }
            $idsVistos[] = $cid;
            $sumaDetalles += (int)$d['votos_obtenidos'];
        }

        // 5. Los candidatos deben pertenecer a la elección del acta
        $placeholders = implode(',', array_fill(0, count($idsVistos), '?'));
        $sqlCands = "SELECT c.id
                     FROM candidatos c
                     JOIN cargos ca ON ca.id = c.cargo_id
                     WHERE ca.eleccion_id = ? AND c.activo = 1
                       AND c.id IN ({$placeholders})";
        $stmt = $this->db->prepare($sqlCands);
        $stmt->execute(array_merge([(int)$acta['eleccion_id']], $idsVistos));
        $validos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $ajenos = array_diff($idsVistos, array_map('intval', $validos));
        if ($ajenos) {
            throw new AppException('Candidatos ajenos a esta elección: ' . implode(', ', $ajenos));
        }

        // 6. Cuadre de totales
        $totalEsperado = ($acta['votos_validos'] ?? 0)
                       + ($acta['votos_blancos'] ?? 0)
                       + ($acta['votos_nulos'] ?? 0)
                       + ($acta['votos_impugnados'] ?? 0);

        if ($sumaDetalles !== (int)($acta['votos_validos'] ?? 0)) {
            throw new AppException(
                "La suma de votos por candidato ({$sumaDetalles}) no coincide " .
                "con votos_validos ({$acta['votos_validos']})"
            );
        }

        if ($totalEsperado !== (int)($acta['total_votantes'] ?? 0)) {
            throw new AppException(
                "El total de votantes no coincide con la suma de votos"
            );
        }
    }

    /** Verifica acta (cambia estado) */
    public function verificar(int $actaId, int $verificadorId): bool
    {
        return $this->update($actaId, [
            'estado'         => 'VERIFICADA',
            'verificado_por' => $verificadorId
        ]);
    }

    private function registrarAuditoria(int $userId, string $accion, string $tabla, $registroId): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO auditoria (usuario_id, tabla, registro_id, accion, ip)
             VALUES (:uid, :tab, :rid, :acc, :ip)"
        );
        $stmt->execute([
            ':uid' => $userId,
            ':tab' => $tabla,
            ':rid' => (string) $registroId,
            ':acc' => $accion,
            ':ip'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    }
}