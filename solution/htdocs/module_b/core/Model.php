<?php

namespace Core;

/**
 * 기본 모델 클래스 — 공통 CRUD 메서드를 제공합니다.
 * 각 모델에서 $table을 선언하고 필요한 메서드를 오버라이드하세요.
 */
abstract class Model
{
    protected static string $table      = '';
    protected static string $primaryKey = 'id';

    private static ?\PDO $connection = null;

    protected static function db(): \PDO
    {
        if (self::$connection === null) {
            $c = require __DIR__ . '/../config/database.php';
            // socket 키가 있으면 Unix 소켓, 없으면 host:port TCP 연결
            $dsn = isset($c['socket'])
                ? "mysql:unix_socket={$c['socket']};dbname={$c['dbname']};charset=utf8mb4"
                : "mysql:host={$c['host']};port={$c['port']};dbname={$c['dbname']};charset=utf8mb4";
            self::$connection = new \PDO(
                $dsn,
                $c['user'],
                $c['password'],
                [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES   => false,   // INT 컬럼을 PHP int로 반환
                    \PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]
            );
        }
        return self::$connection;
    }

    public static function all(): array
    {
        return static::db()->query('SELECT * FROM ' . static::$table)->fetchAll();
    }

    public static function find(int|string $id): array|false
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function where(string $column, mixed $value): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM ' . static::$table . " WHERE {$column} = ?"
        );
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $cols   = implode(', ', array_keys($data));
        $places = implode(', ', array_fill(0, count($data), '?'));

        static::db()
            ->prepare("INSERT INTO " . static::$table . " ({$cols}) VALUES ({$places})")
            ->execute(array_values($data));

        return (int) static::db()->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $stmt = static::db()->prepare(
            "UPDATE " . static::$table . " SET {$sets} WHERE " . static::$primaryKey . " = ?"
        );
        $stmt->execute([...array_values($data), $id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int|string $id): bool
    {
        $stmt = static::db()->prepare(
            'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?'
        );
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
