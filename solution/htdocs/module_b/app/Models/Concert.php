<?php

namespace App\Models;

use Core\Model;

class Concert extends Model
{
    protected static string $table = 'concerts';

    /** 검색/필터/정렬 적용 목록. remaining(잔여석) 포함. */
    public static function getAll(string $search = '', string $venue = '', string $order = 'desc'): array
    {
        $order  = strtolower($order) === 'asc' ? 'ASC' : 'DESC';
        $sql    = 'SELECT c.*, (c.total_seats - COUNT(bs.id)) AS remaining
                   FROM concerts c
                   LEFT JOIN booking_seats bs ON bs.concert_id = c.id';
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]  = 'c.title LIKE ?';
            $params[] = "%{$search}%";
        }
        if ($venue !== '') {
            $where[]  = 'c.venue LIKE ?';
            $params[] = "{$venue}%";
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= " GROUP BY c.id ORDER BY c.date {$order}";

        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** 단건 조회 + remaining 포함 */
    public static function findWithRemaining(int $id): array|false
    {
        $stmt = static::db()->prepare(
            'SELECT c.*, (c.total_seats - COUNT(bs.id)) AS remaining
             FROM concerts c
             LEFT JOIN booking_seats bs ON bs.concert_id = c.id
             WHERE c.id = ?
             GROUP BY c.id'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** 장소 필터 드롭다운용 도시명 목록 */
    public static function getCities(): array
    {
        $venues = static::db()
            ->query('SELECT DISTINCT venue FROM concerts ORDER BY venue')
            ->fetchAll(\PDO::FETCH_COLUMN);

        $cities = array_unique(array_map(fn($v) => explode(' ', $v)[0], $venues));
        sort($cities);
        return $cities;
    }
}
