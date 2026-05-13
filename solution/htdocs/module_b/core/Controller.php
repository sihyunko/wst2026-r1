<?php

namespace Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): Response
    {
        return new Response(View::render($template, $data));
    }

    // BASE_URL을 자동으로 앞에 붙여 XAMPP 서브디렉터리에서도 정상 동작합니다.
    protected function redirect(string $path, int $status = 302): Response
    {
        $url = (defined('BASE_URL') ? BASE_URL : '') . $path;
        return Response::redirect($url, $status);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    protected function userName(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    // 비로그인 시 /login으로 리다이렉트. null이면 통과.
    protected function requireAuth(string $message = '로그인이 필요합니다.'): ?Response
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => $message];
            return $this->redirect('/login');
        }
        return null;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = compact('type', 'message');
    }
}
