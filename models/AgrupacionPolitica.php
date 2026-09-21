<?php
declare(strict_types=1);

class AgrupacionPolitica extends Model
{
    protected string $table = 'agrupaciones_politicas';

    /** Lista agrupaciones con sus candidatos por cargo */
    public function conCandidatos(int $cargoId): array
    {
        return $this->query(
            "SELECT ap.*, COUNT(c.id) AS total_candidatos
             FROM agrupaciones_politicas ap
             LEFT JOIN candidatos c ON c.agrupacion_id = ap.id AND c.cargo_id = :cid
             WHERE ap.activo = 1
             GROUP BY ap.id
             ORDER BY ap.nombre",
            [':cid' => $cargoId]
        );
    }
}