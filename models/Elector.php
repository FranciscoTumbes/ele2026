<?php
declare(strict_types=1);

class Elector extends Model
{
    protected string $table = 'electores';

    /** Busca elector por DNI (para validar en mesa) */
    public function porDni(string $dni): ?array
    {
        if (!Candidato::validarDni($dni)) {
            throw new AppException('DNI inválido');
        }
        return $this->queryOne(
            "SELECT e.*, m.numero_mesa, cv.nombre AS centro
             FROM electores e
             JOIN mesas_sufragio m ON m.id = e.mesa_id
             JOIN centros_votacion cv ON cv.id = m.centro_id
             WHERE e.dni = :dni",
            [':dni' => $dni]
        );
    }

    /** Marca al elector como ya votó (estado TAQUILLADO) */
    public function marcarVoto(int $id): bool
    {
        return $this->update($id, ['estado' => 'TAQUILLADO']);
    }

    /** Estadísticas de participación por mesa */
    public function participacionMesa(int $mesaId): array
    {
        return $this->queryOne(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado='TAQUILLADO' THEN 1 ELSE 0 END) AS votaron,
                SUM(CASE WHEN estado='HABILITADO' THEN 1 ELSE 0 END) AS pendientes
             FROM electores WHERE mesa_id = :mid",
            [':mid' => $mesaId]
        );
    }
}