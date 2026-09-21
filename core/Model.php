<?php
declare(strict_types=1);

/**
 * Clase Model - DAO genérico con operaciones CRUD básicas
 * Todas las entidades heredan de esta clase
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Busca un registro por su ID */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /** Obtiene todos los registros activos */
    public function all(bool $soloActivos = true): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($soloActivos && $this->hasColumn('activo')) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY {$this->primaryKey} ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /** Inserta un nuevo registro y retorna su ID */
    public function create(array $data): int
    {
        $data = $this->filterColumns($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindParams($data));

        return (int) $this->db->lastInsertId();
    }

    /** Actualiza un registro por ID */
    public function update(int $id, array $data): bool
    {
        $data = $this->filterColumns($data);
        $set = implode(', ', array_map(fn($col) => "{$col} = :{$col}", array_keys($data)));

        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :id";
        $data[':id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /** Soft delete (cambia activo=0) */
    public function delete(int $id): bool
    {
        if ($this->hasColumn('activo')) {
            return $this->update($id, ['activo' => 0]);
        }
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->prepare($sql)->execute([':id' => $id]);
    }

    /** Cuenta registros con condiciones */
    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Ejecuta una consulta con parámetros */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ejecuta consulta y retorna un único registro */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /** Filtra columnas válidas de la tabla */
    private function filterColumns(array $data): array
    {
        $cols = $this->getTableColumns();
        return array_intersect_key($data, array_flip($cols));
    }

    /** Prepara parámetros con prefijo ":" */
    private function bindParams(array $data): array
    {
        $bound = [];
        foreach ($data as $key => $value) {
            $bound[":{$key}"] = $value;
        }
        return $bound;
    }

    /** Verifica si existe una columna en la tabla */
    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->getTableColumns());
    }

    /** Cache de columnas de la tabla */
    private function getTableColumns(): array
    {
        static $cache = [];
        if (!isset($cache[$this->table])) {
            $stmt = $this->db->query("DESCRIBE {$this->table}");
            $cache[$this->table] = array_column($stmt->fetchAll(), 'Field');
        }
        return $cache[$this->table];
    }
}