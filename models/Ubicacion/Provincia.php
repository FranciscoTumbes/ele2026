<?php
declare(strict_types=1);

class Provincia extends Model
{
    protected string $table = 'provincias';

    /** Lista provincias de una región (por defecto: primera región del sistema) */
    public function porRegion(?int $regionId = null): array
    {
        if ($regionId === null || $regionId <= 0) {
            $row = $this->queryOne("SELECT id FROM regiones ORDER BY id LIMIT 1");
            $regionId = (int)($row['id'] ?? 0);
        }
        return $this->query(
            "SELECT * FROM provincias WHERE region_id = :rid AND activo = 1 ORDER BY nombre",
            [':rid' => $regionId]
        );
    }
}
