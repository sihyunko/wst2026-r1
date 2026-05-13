<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Booking;
use App\Models\BookingSeat;

class BookingController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->requireAuth()) return $guard;

        return $this->view('bookings.index', [
            'title'    => '예매 내역 - TicketBox',
            'bookings' => Booking::findByUser($this->userId()),
            'newId'    => (int) $request->query('new', 0),
        ]);
    }

    public function show(Request $request, array $params): Response
    {
        if ($guard = $this->requireAuth()) return $guard;

        $booking = Booking::findWithConcert((int) $params['id']);
        if (!$booking || (int)$booking['user_id'] !== $this->userId()) {
            return new Response('<h1>예매 내역을 찾을 수 없습니다.</h1>', 404);
        }

        $rows  = BookingSeat::where('booking_id', $booking['id']);
        $seats = array_column($rows, 'seat');

        return $this->view('bookings.show', [
            'title'   => '예매 상세 - TicketBox',
            'booking' => $booking,
            'seats'   => $seats,
        ]);
    }

    public function cancel(Request $request, array $params): Response
    {
        if ($guard = $this->requireAuth()) return $guard;

        Booking::cancel((int) $params['id'], $this->userId());

        return $this->redirect('/bookings');
    }
}
