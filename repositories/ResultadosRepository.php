<?php
declare(strict_types=1);

class ResultadosRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Totalización general por candidato para un cargo
     */
    public function totalizacionCargo(int $eleccionId, int $cargoId, ?int $ambitoId = null): array
    {
        $sql = "SELECT
                    c.id AS candidato_id,
                    CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS candidato,
                    ap.siglas,
                    ap.nombre AS agrupacion,
                    ap.color_hex,
                    COALESCE(SUM(dac.votos_obtenidos), 0) AS total_votos
                FROM candidatos c
                JOIN agrupaciones_politicas ap ON ap.id = c.agrupacion_id
                LEFT JOIN detalle_acta_candidato dac ON dac.candidato_id = c.id
                LEFT JOIN actas_sufragio a ON a.id = dac.acta_id
                    AND a.eleccion_id = :eid AND a.estado = 'VERIFICADA'
                WHERE c.cargo_id = :cid AND c.activo = 1";
        $params = [':eid' => $eleccionId, ':cid' => $cargoId];

        if ($ambitoId !== null) {
            $sql .= " AND c.ambito_id = :aid";
            $params[':aid'] = $ambitoId;
        }

        $sql .= " GROUP BY c.id ORDER BY total_votos DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $candidatos = $stmt->fetchAll();

        // Calcula porcentajes
        $totalGeneral = array_sum(array_column($candidatos, 'total_votos'));
        foreach ($candidatos as &$c) {
            $c['porcentaje'] = $totalGeneral > 0
                ? round(($c['total_votos'] / $totalGeneral) * 100, 2)
                : 0;
        }

        return [
            'total_votos_validos' => $totalGeneral,
            'candidatos'          => $candidatos
        ];
    }

    /**
     * Avance de digitación por provincia
     */
    public function avancePorProvincia(int $eleccionId): array
    {
        $sql = "SELECT
                    p.id, p.nombre AS provincia,
                    COUNT(DISTINCT m.id) AS mesas_total,
                    COUNT(DISTINCT CASE WHEN a.estado IN ('DIGITADA','VERIFICADA') THEN m.id END) AS mesas_procesadas,
                    ROUND(
                        COUNT(DISTINCT CASE WHEN a.estado IN ('DIGITADA','VERIFICADA') THEN m.id END) * 100.0
                        / NULLIF(COUNT(DISTINCT m.id), 0), 2
                    ) AS porcentaje_avance
                FROM provincias p
                JOIN distritos d ON d.provincia_id = p.id
                JOIN centros_votacion cv ON cv.distrito_id = d.id
                JOIN mesas_sufragio m ON m.centro_id = cv.id
                LEFT JOIN actas_sufragio a ON a.mesa_id = m.id AND a.eleccion_id = :eid
                WHERE p.region_id = (SELECT region_id FROM elecciones WHERE id = :eid2)
                GROUP BY p.id
                ORDER BY p.nombre";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':eid' => $eleccionId, ':eid2' => $eleccionId]);
        return $stmt->fetchAll();
    }

    /**
     * Resumen general de una elección
     */
    public function resumenEleccion(int $eleccionId): array
    {
        $sql = "SELECT
                    SUM(a.electores_habilitados) AS electores_habilitados,
                    SUM(a.total_votantes) AS total_votantes,
                    SUM(a.votos_validos) AS votos_validos,
                    SUM(a.votos_blancos) AS votos_blancos,
                    SUM(a.votos_nulos) AS votos_nulos,
                    SUM(a.votos_impugnados) AS votos_impugnados,
                    COUNT(DISTINCT a.mesa_id) AS mesas_procesadas
                FROM actas_sufragio a
                WHERE a.eleccion_id = :eid AND a.estado IN ('DIGITADA','VERIFICADA')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':eid' => $eleccionId]);
        return $stmt->fetch();
    }
}