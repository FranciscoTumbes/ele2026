<?php
declare(strict_types=1);

class CentroVotacion extends Model
{
    protected string $table = 'centros_votacion';

    public function porDistrito(int $distritoId): array
    {
        return $this->query(
            "SELECT * FROM centros_votacion WHERE distrito_id = :did AND activo = 1 ORDER BY nombre",
            [':did' => $distritoId]
        );
    }

    /** Centros con nombre de distrito */
    public function listarCompleto(int $distritoId): array
    {
        return $this->query(
            "SELECT cv.*, d.nombre AS distrito
             FROM centros_votacion cv
             JOIN distritos d ON d.id = cv.distrito_id
             WHERE cv.distrito_id = :did AND cv.activo = 1
             ORDER BY cv.nombre",
            [':did' => $distritoId]
        );
    }
}
