<?php
declare(strict_types=1);

/**
 * Encapsula la petición HTTP entrante
 */
class Request
{
    private string $method;
    private string $uri;
    private array $query;
    private array $post;
    private array $server;
    private array $params = []; // Parámetros de ruta (ej: /actas/{id})
    private ?array $jsonBody = null;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri    = $this->parseUri();
        $this->query  = $_GET;
        $this->post   = $_POST;
        $this->server = $_SERVER;
    }

    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Extrae el directorio base de la ejecución (ej: /ele2026/public)
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        
        // Remueve el directorio base de la URI si está presente
        if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        return rtrim($uri, '/') ?: '/';
    }

    public function method(): string { return $this->method; }
    public function uri(): string  { return $this->uri; }

    public function query(string $key = null, $default = null)
    {
        return $key === null ? $this->query : ($this->query[$key] ?? $default);
    }

    public function post(string $key = null, $default = null)
    {
        return $key === null ? $this->post : ($this->post[$key] ?? $default);
    }

    /** Obtiene input desde POST, GET o JSON body */
    public function input(string $key = null, $default = null)
    {
        $data = array_merge($this->query, $this->post, $this->json() ?? []);
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    /** Parsea body JSON si Content-Type es application/json */
    public function json(): ?array
    {
        if ($this->jsonBody === null) {
            $contentType = $this->server['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') !== false) {
                $raw = file_get_contents('php://input');
                $this->jsonBody = json_decode($raw, true) ?: [];
            } else {
                $this->jsonBody = [];
            }
        }
        return $this->jsonBody ?: null;
    }

    public function setParams(array $params): void { $this->params = $params; }
    public function param(string $key, $default = null) { return $this->params[$key] ?? $default; }

    public function isAjax(): bool
    {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}