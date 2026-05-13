<?php

namespace Core;

/**
 * PSR-4 호환 오토로더
 * Composer 없이도 네임스페이스-파일 매핑을 처리합니다.
 */
class Autoloader
{
    private array $prefixes = [];

    public function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix  = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->prefixes[$prefix][] = $baseDir;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    private function load(string $class): void
    {
        $prefix = $class;

        while (false !== ($pos = strrpos($prefix, '\\'))) {
            $prefix        = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);

            if (isset($this->prefixes[$prefix])) {
                foreach ($this->prefixes[$prefix] as $baseDir) {
                    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
                    if (file_exists($file)) {
                        require $file;
                        return;
                    }
                }
            }

            $prefix = rtrim($prefix, '\\');
        }
    }
}
