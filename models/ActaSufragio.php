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
        $sumaDetalles = array_sum(array_column($detalles, 'votos_obtenidos'));
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