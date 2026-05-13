<?php

namespace Core;

class Application
{
    private static self $instance;

    private Router  $router;
    private Request $request;

    public function __construct(private readonly array $config = [])
    {
        self::$instance = $this;
        $this->router   = new Router();
        $this->request  = new Request();
    }

    public static function getInstance(): self { return self::$instance; }
    public function getRouter(): Router        { return $this->router; }
    public function getRequest(): Request      { return $this->request; }

    public function config(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function run(): void
    {
        $response = $this->router->dispatch($this->request);
        $response->send();
    }
}
