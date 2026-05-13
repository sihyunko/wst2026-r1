<?php

namespace Core;

class Router
{
    /** @var array{method: string, path: string, handler: string|callable}[] */
    private array $routes = [];

    public function get(string $path, string|callable $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'path' => $path, 'handler' => $handler];
    }

    public function post(string $path, string|callable $handler): void
    {
        $this->routes[] = ['method' => 'POST', 'path' => $path, 'handler' => $handler];
    }

    public function put(string $path, string|callable $handler): void
    {
        $this->routes[] = ['method' => 'PUT', 'path' => $path, 'handler' => $handler];
    }

    public function delete(string $path, string|callable $handler): void
    {
        $this->routes[] = ['method' => 'DELETE', 'path' => $path, 'handler' => $handler];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $params = $this->match($route['path'], $request->uri());
            if ($params === null) {
                continue;
            }

            return $this->handle($route['handler'], $request, $params);
        }

        return new Response('<h1>404 Not Found</h1>', 404);
    }

    /**
     * {param} 형태의 라우트 파라미터를 정규식으로 변환해 URI와 매칭합니다.
     * 매칭 성공 시 캡처된 파라미터 배열, 실패 시 null 반환.
     */
    private function match(string $path, string $uri): ?array
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);

        if (!preg_match('@^' . $pattern . '$@', $uri, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function handle(string|callable $handler, Request $request, array $params): Response
    {
        if (is_callable($handler)) {
            return $this->toResponse($handler($request, $params));
        }

        // "ControllerName@method" 형식
        [$class, $method] = explode('@', $handler);
        $fqcn = "App\\Controllers\\{$class}";

        $controller = new $fqcn();
        return $this->toResponse($controller->$method($request, $params));
    }

    private function toResponse(mixed $result): Response
    {
        return $result instanceof Response ? $result : new Response((string) $result);
    }
}
