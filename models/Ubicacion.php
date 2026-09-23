<?php
declare(strict_types=1);

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

    /**
     * Busca una mesa por número de mesa, incluyendo datos de centro, distrito y el
     * estado del acta PARA LA ELECCIÓN INDICADA (si existe).
     *
     * @param string   $numero
     * @param int|null $eleccionId Si se omite, se devuelve la primera mesa coincidente.
     * @return array|null
     */
    public function buscarPorNumero(string $numero, ?int $eleccionId = null): ?array
    {
        $sql = "SELECT m.*, cv.codigo AS centro_codigo, cv.nombre AS centro_nombre,
                       d.id AS distrito_id, d.nombre AS distrito_nombre,
                       a.estado AS acta_estado
                FROM mesas_sufragio m
                JOIN centros_votacion cv ON cv.id = m.centro_id
                JOIN distritos d ON d.id = cv.distrito_id
                LEFT JOIN actas_sufragio a
                    ON a.mesa_id = m.id
                   AND (:eid IS NULL OR a.eleccion_id = :eid)
                WHERE m.numero_mesa = :numero
                ORDER BY m.id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':numero' => $numero, ':eid' => $eleccionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}