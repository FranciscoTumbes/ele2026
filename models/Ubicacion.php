<?php
declare(strict_types=1);

class Provincia extends Model
{
    protected string $table = 'provincias';

    /** Lista provincias de una región */
    public function porRegion(int $regionId): array
    {
        return $this->query(
            "SELECT * FROM provincias WHERE region_id = :rid AND activo = 1 ORDER BY nombre",
            [':rid' => $regionId]
        );
    }
}

class Distrito extends Model
{
    protected string $table = 'distritos';

    public function porProvincia(int $provinciaId): array
    {
        return $this->query(
            "SELECT * FROM distritos WHERE provincia_id = :pid AND activo = 1 ORDER BY nombre",
            [':pid' => $provinciaId]
        );
    }
}

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

class MesaSufragio extends Model
{
    protected string $table = 'mesas_sufragio';

    public function porCentro(int $centroId): array
    {
        return $this->query(
            "SELECT * FROM mesas_sufragio WHERE centro_id = :cid ORDER BY numero_mesa",
            [':cid' => $centroId]
        );
    }
}