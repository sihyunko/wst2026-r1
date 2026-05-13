<?php

namespace App\Models;

use Core\Model;

class BookingSeat extends Model
{
    protected static string $table = 'booking_seats';

    /** 해당 공연의 이미 예매된 좌석 번호 배열 */
    public static function bookedSeats(int $concertId): array
    {
        $stmt = static::db()->prepare(
            'SELECT seat FROM booking_seats WHERE concert_id = ?'
        );
        $stmt->execute([$concertId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
