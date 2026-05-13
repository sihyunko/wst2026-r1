<?php

namespace Core;

class Request
{
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // 서브디렉터리 설치를 위해 스크립트 경로 prefix를 제거합니다.
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        $rawPath   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path      = $scriptDir ? substr($rawPath, strlen($scriptDir)) : $rawPath;

        $this->uri = '/' . ltrim($path ?: '/', '/');
    }

    public function method(): string { return $this->method; }
    public function uri(): string    { return $this->uri; }
    public function isPost(): bool   { return $this->method === 'POST'; }
    public function isGet(): bool    { return $this->method === 'GET'; }

    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /** GET + POST 전체 */
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }
}
