<?php

namespace Core;

class View
{
    private static string  $path      = __DIR__ . '/../app/Views';
    private static ?string $layout    = null;
    private static string  $scriptBuf = '';
    private static string  $styleBuf  = '';

    public static function setPath(string $path): void     { self::$path   = $path; }
    public static function setLayout(?string $layout): void { self::$layout = $layout; }

    // 뷰 파일 안에서 호출 → 레이아웃의 </body> 직전에 출력됩니다.
    public static function pushScript(string $js): void  { self::$scriptBuf .= $js; }
    public static function pushStyle(string $css): void  { self::$styleBuf  .= $css; }

    public static function flushScript(): string
    {
        $v = self::$scriptBuf;
        self::$scriptBuf = '';
        return $v;
    }

    public static function flushStyle(): string
    {
        $v = self::$styleBuf;
        self::$styleBuf = '';
        return $v;
    }

    public static function render(string $template, array $data = []): string
    {
        $file = self::$path . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View [{$template}] not found: {$file}");
        }

        $content = self::capture($file, $data);

        if (self::$layout !== null) {
            $layoutFile = self::$path . '/layouts/' . self::$layout . '.php';
            if (file_exists($layoutFile)) {
                $content = self::capture($layoutFile, array_merge($data, ['content' => $content]));
            }
        }

        return $content;
    }

    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}