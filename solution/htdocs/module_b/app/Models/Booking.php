<?php

namespace App\Models;

use Core\Model;

class Booking extends Model
{
    protected static string $table = 'bookings';

    public static function findByUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            'SELECT b.*, c.title, c.date, c.venue, COUNT(bs.id) AS seat_count
             FROM bookings b
             JOIN concerts c ON c.id = b.concert_id
             LEFT JOIN booking_seats bs ON bs.booking_id = b.id
             WHERE b.user_id = ?
             GROUP BY b.id
             ORDER BY b.booked_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findWithConcert(int $bookingId): array|false
    {
        $stmt = static::db()->prepare(
            'SELECT b.*, c.title, c.date, c.venue, c.price
             FROM bookings b
             JOIN concerts c ON c.id = b.concert_id
             WHERE b.id = ?'
        );
        $stmt->execute([$bookingId]);
        return $stmt->fetch();
    }

    public static function createWithSeats(int $userId, int $concertId, array $seats): array
    {
        $db = static::db();
        $db->beginTransaction();

        try {
            $placeholders = implode(',', array_fill(0, count($seats), '?'));
            $stmt = $db->prepare(
                "SELECT seat FROM booking_seats
                 WHERE concert_id = ? AND seat IN ({$placeholders})
                 FOR UPDATE"
            );
            $stmt->execute(array_merge([$concertId], $seats));
            $alreadyBooked = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!empty($alreadyBooked)) {
                $db->rollBack();
                return ['error' => true, 'seats' => $alreadyBooked];
            }

            $totalPrice = count($seats) * 5000;
            $db->prepare('INSERT INTO bookings (user_id, concert_id, total_price) VALUES (?, ?, ?)')
               ->execute([$userId, $concertId, $totalPrice]);
            $bookingId = (int) $db->lastInsertId();

            $seatStmt = $db->prepare(
                'INSERT INTO booking_seats (booking_id, concert_id, seat) VALUES (?, ?, ?)'
            );
            foreach ($seats as $seat) {
                $seatStmt->execute([$bookingId, $concertId, $seat]);
            }

            $db->commit();
            return ['error' => false, 'booking_id' => $bookingId];

        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cancel(int $bookingId, int $userId): bool
    {
        $db = static::db();

        $stmt = $db->prepare('SELECT id FROM bookings WHERE id = ? AND user_id = ?');
        $stmt->execute([$bookingId, $userId]);
        if (!$stmt->fetch()) {
            return false;
        }

        $db->prepare('DELETE FROM booking_seats WHERE booking_id = ?')->execute([$bookingId]);
        $db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$bookingId]);
        return true;
    }
}
