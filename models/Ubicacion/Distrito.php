<?php
declare(strict_types=1);

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
