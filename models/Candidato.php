<?php
declare(strict_types=1);

class Candidato extends Model
{
    protected string $table = 'candidatos';

    /** Lista candidatos por elección y cargo */
    public function porEleccionYCargo(int $eleccionId, int $cargoId, ?int $ambitoId = null): array
    {
        $sql = "SELECT c.*, ap.siglas, ap.nombre AS agrupacion, ap.color_hex,
                       ap.logo_url
                FROM candidatos c
                JOIN agrupaciones_politicas ap ON ap.id = c.agrupacion_id
                JOIN cargos ca ON ca.id = c.cargo_id
                WHERE ca.eleccion_id = :eid AND c.cargo_id = :cid AND c.activo = 1";
        $params = [':eid' => $eleccionId, ':cid' => $cargoId];

        if ($ambitoId !== null) {
            $sql .= " AND c.ambito_id = :aid";
            $params[':aid'] = $ambitoId;
        }
        $sql .= " ORDER BY ap.nombre, c.posicion_lista";

        return $this->query($sql, $params);
    }

    /** Valida DNI peruano (8 dígitos) */
    public static function validarDni(string $dni): bool
    {
        return (bool) preg_match('/^\d{8}$/', $dni);
    }
}