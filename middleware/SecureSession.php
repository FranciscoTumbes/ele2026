<?php
declare(strict_types=1);

/**
 * SecureSession - Configuración centralizada y segura de sesiones PHP.
 *  - Cookies HttpOnly, SameSite=Lax (compatibles con sesión por cookie)
 *  - Expiración por inactividad
 *  - Regeneración de ID (previene session fixation)
 */
class SecureSession
{
    private const INACTIVITY_TIMEOUT = 1800; // 30 minutos

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => !empty($_SERVER['HTTPS']), // true en producción con TLS
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_name('ELE2026SESSID');
        session_start();

        self::enforceInactivityTimeout();
    }

    /** Cierra la sesión si excedió el tiempo de inactividad. */
    public static function enforceInactivityTimeout(): void
    {
        $now  = time();
        $last = $_SESSION['last_activity'] ?? null;

        if ($last !== null && ($now - (int)$last) > self::INACTIVITY_TIMEOUT) {
            self::destroy();
            return;
        }
        $_SESSION['last_activity'] = $now;
    }

    /** Regenera el ID de sesión (llamar tras login/privilegio). */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /** Destruye la sesión por completo. */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
