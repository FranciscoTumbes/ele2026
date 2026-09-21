<?php
declare(strict_types=1);

class Eleccion extends Model
{
    protected string $table = 'elecciones';

    /** Elecciones activas por tipo */
    public function activasPorTipo(string $tipo): array
    {
        return $this->query(
            "SELECT * FROM elecciones
             WHERE tipo = :tipo AND estado IN ('CONVOCADA','EN_CURSO')
             ORDER BY fecha_eleccion DESC",
            [':tipo' => $tipo]
        );
    }

    /** Cambia estado de la elección */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $validos = ['CONVOCADA', 'EN_CURSO', 'FINALIZADA', 'ANULADA'];
        if (!in_array($estado, $validos)) {
            throw new AppException("Estado inválido: {$estado}");
        }
        return $this->update($id, ['estado' => $estado]);
    }
}