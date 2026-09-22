<?php
declare(strict_types=1);

/**
 * Verifica que el usuario esté autenticado antes de acceder a rutas protegidas
 */
class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            // Si es AJAX responde JSON, si no redirige a login
            if ($request->isAjax()) {
                Response::error('Sesión expirada. Inicie sesión nuevamente.', 401);
            }
            Response::redirect('/login');
        }

        // Opcional: verificar roles específicos
        // if (!in_array($_SESSION['user']['rol'], ['ADMIN','JURADO'])) {
        //     Response::error('No autorizado', 403);
        // }
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

        if (($_SESSION['user']['rol'] ?? '') !== 'ADMIN') {
            Response::error('Acceso restringido a administradores', 403);
        }
    }
}
