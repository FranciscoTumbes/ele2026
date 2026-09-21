<?php
declare(strict_types=1);

class Usuario extends Model
{
    protected string $table = 'usuarios';

    /**
     * Registra un nuevo usuario con contraseña hasheada
     */
    public function registrar(array $data): int
    {
        if (empty($data['password']) || strlen($data['password']) < 8) {
            throw new AppException('La contraseña debe tener al menos 8 caracteres');
        }
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        unset($data['password']);
        return $this->create($data);
    }

    /**
     * Autentica usuario y retorna datos (sin password)
     */
    public function autenticar(string $username, string $password): ?array
    {
        $user = $this->queryOne(
            "SELECT u.*, r.nombre AS rol
             FROM usuarios u
             JOIN roles r ON r.id = u.rol_id
             WHERE u.username = :u AND u.activo = 1",
            [':u' => $username]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Actualiza último acceso
        $this->update((int) $user['id'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);

        unset($user['password_hash']);
        return $user;
    }

    /** Inicia sesión y guarda token en tabla sesiones */
    public function iniciarSesion(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->db->prepare(
            "INSERT INTO sesiones_usuario (usuario_id, token, ip, user_agent)
             VALUES (:uid, :tok, :ip, :ua)"
        );
        $stmt->execute([
            ':uid' => $userId,
            ':tok' => hash('sha256', $token),
            ':ip'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ':ua'  => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        return $token;
    }
}