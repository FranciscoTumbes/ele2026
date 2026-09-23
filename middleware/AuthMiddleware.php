<?php
declare(strict_types=1);

/**
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas
 */
class AuthMiddleware
{
    public function handle(Request $request): void
    {
        SecureSession::start();

        if (empty($_SESSION['user'])) {
            // Si es AJAX responde JSON, si no redirige a login
            if ($request->isAjax()) {
                Response::error('Sesión expirada. Inicie sesión nuevamente.', 401);
            }
            Response::redirect('/login');
        }
    }
}

/**
 * Middleware específico para rol ADMIN
 */
class AdminMiddleware
{
    public function handle(Request $request): void
    {
        (new AuthMiddleware())->handle($request);

        SecureSession::start();
        if (!$this->isAdmin($_SESSION['user'] ?? [])) {
            Response::error('Acceso restringido a administradores', 403);
        }
    }

    /**
     * El rol puede venir como string (nombre, ej. 'ADMIN') o como ID numérico
     * de la tabla roles (1 = ADMIN según cpiset.sql).
     */
    public static function isAdmin(array $user): bool
    {
        $rol = $user['rol'] ?? null;
        return $rol === 'ADMIN' || (int)$rol === 1;
    }
}

/**
 * Middleware para roles JURADO o ADMIN (verificación de actas)
 */
class JuradoMiddleware
{
    public function handle(Request $request): void
    {
        (new AuthMiddleware())->handle($request);

        SecureSession::start();
        $user = $_SESSION['user'] ?? [];
        $rol  = $user['rol'] ?? null;
        $esJurado = ($rol === 'JURADO') || ((int)$rol === 2 && !AdminMiddleware::isAdmin($user));

        if (!$esJurado && !AdminMiddleware::isAdmin($user)) {
            Response::error('Acceso restringido a jurados y administradores', 403);
        }
    }
}
