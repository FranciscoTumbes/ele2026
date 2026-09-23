<?php
declare(strict_types=1);

/**
 * Controlador base - Provee utilidades comunes
 */
abstract class Controller
{
    /** Obtiene el usuario autenticado desde sesión */
    protected function currentUser(): ?array
    {
        SecureSession::start();
        return $_SESSION['user'] ?? null;
    }

    /** Requiere usuario autenticado o deniega */
    protected function requireAuth(): array
    {
        $user = $this->currentUser();
        if (!$user) {
            Response::error('No autenticado', 401);
        }
        return $user;
    }

    /** Valida campos requeridos */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;

            if (str_contains($rule, 'required') && ($value === null || $value === '')) {
                $errors[$field] = "El campo '{$field}' es obligatorio";
                continue;
            }

            if (str_contains($rule, 'numeric') && $value !== null && !is_numeric($value)) {
                $errors[$field] = "El campo '{$field}' debe ser numérico";
            }

            if (preg_match('/max:(\d+)/', $rule, $m)) {
                if (strlen((string)$value) > (int)$m[1]) {
                    $errors[$field] = "El campo '{$field}' excede {$m[1]} caracteres";
                }
            }
        }

        if (!empty($errors)) {
            Response::error('Error de validación', 422, $errors);
        }
        return $data;
    }

    /** Helper para responder JSON */
    protected function json($data, int $status = 200): void
    {
        Response::success($data, 'OK', $status);
    }
}