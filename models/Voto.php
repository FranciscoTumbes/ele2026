<?php
declare(strict_types=1);

class Voto extends Model
{
    protected string $table = 'votos';

    /**
     * Registra un voto individual (transaccional)
     */
    public function registrar(array $data, int $usuarioId): int
    {
        $this->validarDatos($data);
        $data['usuario_id'] = $usuarioId;
        $data['hora_registro'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    private function validarDatos(array $data): void
    {
        if (empty($data['mesa_id']) || empty($data['eleccion_id'])) {
            throw new AppException('Mesa y elección son obligatorios');
        }
        if ($data['tipo_voto'] === 'VALIDO' && empty($data['candidato_id'])) {
            throw new AppException('Un voto válido requiere candidato');
        }
    }

    /** Total de votos por candidato en una elección */
    public function totalPorCandidato(int $eleccionId): array
    {
        return $this->query(
            "SELECT c.id, c.nombres, c.apellido_paterno,
                    ap.siglas, ap.color_hex, COUNT(v.id) AS total
             FROM votos v
             JOIN candidatos c ON c.id = v.candidato_id
             JOIN agrupaciones_politicas ap ON ap.id = c.agrupacion_id
             WHERE v.eleccion_id = :eid AND v.tipo_voto = 'VALIDO'
             GROUP BY c.id
             ORDER BY total DESC",
            [':eid' => $eleccionId]
        );
    }
}