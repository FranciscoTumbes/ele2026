<?php
declare(strict_types=1);

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     */
    public function login(Request $request): void
    {
        $data = $this->validate($request->input(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        $usuarioModel = new Usuario();
        $user = $usuarioModel->autenticar($data['username'], $data['password']);

        if (!$user) {
            Response::error('Credenciales inválidas', 401);
        }

        // Inicia sesión PHP
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time();

        Response::success([
            'user' => $user,
            'redirect' => '/dashboard'
        ], 'Inicio de sesión exitoso');
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        session_destroy();
        Response::success(null, 'Sesión cerrada');
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request): void
    {
        $user = $this->requireAuth();
        Response::success($user);
    }
}
