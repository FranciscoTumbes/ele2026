<?php
declare(strict_types=1);

/**
 * Router - Mapea URIs a controladores con soporte de:
 *   - Parámetros dinámicos: /actas/{id}
 *   - Middlewares por ruta
 *   - Grupos de rutas con prefijo
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $groupStack = [];

    /** Registra una ruta */
    public function add(string $method, string $uri, string|callable $handler, array $middleware = []): self
    {
        $prefix = implode('/', $this->groupStack);
        $fullUri = $prefix ? "/{$prefix}/" . ltrim($uri, '/') : $uri;
        $fullUri = '/' . trim($fullUri, '/');

        $this->routes[] = [
            'method'     => strtoupper($method),
            'uri'        => $fullUri,
            'handler'    => $handler,
            'middleware'  => array_merge($this->currentMiddlewares(), $middleware),
            'pattern'    => $this->compilePattern($fullUri)
        ];
        return $this;
    }

    public function get(string $uri, string|callable $handler, array $mw = []): self    { return $this->add('GET', $uri, $handler, $mw); }
    public function post(string $uri, string|callable $handler, array $mw = []): self   { return $this->add('POST', $uri, $handler, $mw); }
    public function put(string $uri, string|callable $handler, array $mw = []): self    { return $this->add('PUT', $uri, $handler, $mw); }
    public function patch(string $uri, string|callable $handler, array $mw = []): self  { return $this->add('PATCH', $uri, $handler, $mw); }
    public function delete(string $uri, string|callable $handler, array $mw = []): self { return $this->add('DELETE', $uri, $handler, $mw); }

    /** Agrupa rutas bajo un prefijo y middlewares comunes */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $this->groupStack[] = trim($prefix, '/');
        $this->middlewares[] = $middleware;
        $callback($this);
        array_pop($this->groupStack);
        array_pop($this->middlewares);
    }

    private function currentMiddlewares(): array
    {
        return $this->middlewares ? array_merge(...$this->middlewares) : [];
    }

    /** Convierte /actas/{id} en regex /actas/(?P<id>[^/]+) */
    private function compilePattern(string $uri): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    /** Despacha la petición al controlador correspondiente */
    public function dispatch(Request $request): void
    {
        $routeFound = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $request->uri(), $matches)) {
                $routeFound = true;

                if ($route['method'] !== $request->method()) continue;

                // Filtra solo los named groups
                $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Ejecuta middlewares
                foreach ($route['middleware'] as $mwClass) {
                    $mw = new $mwClass();
                    $mw->handle($request); // Si falla, lanza excepción y detiene
                }

                // Ejecuta handler (Controller@method o closure)
                $this->callHandler($route['handler'], $request);
                return;
            }
        }

        if ($routeFound) {
            Response::error('Método no permitido', 405);
        } else {
            // Ruta no encontrada
            Response::error('Ruta no encontrada: ' . $request->uri(), 404);
        }
    }

    private function callHandler($handler, Request $request): void
    {
        if (is_callable($handler)) {
            $handler($request);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            if (!class_exists($class)) {
                Response::error("Controlador no existe: {$class}", 500);
            }
            $controller = new $class();
            if (!method_exists($controller, $method)) {
                Response::error("Método no existe: {$class}@{$method}", 500);
            }
            $controller->$method($request);
            return;
        }

        Response::error('Handler inválido', 500);
    }
}