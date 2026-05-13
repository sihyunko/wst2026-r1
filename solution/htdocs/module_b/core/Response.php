<?php

namespace Core;

class Response
{
    private array $headers = [];

    public function __construct(
        private string $body   = '',
        private int    $status = 200,
    ) {}

    public function header(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public static function redirect(string $url, int $status = 302): static
    {
        return (new static('', $status))->header('Location', $url);
    }

    public static function json(mixed $data, int $status = 200): static
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return (new static($body, $status))->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $this->body;
    }
}
