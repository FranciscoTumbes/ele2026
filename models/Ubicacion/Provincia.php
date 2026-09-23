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
