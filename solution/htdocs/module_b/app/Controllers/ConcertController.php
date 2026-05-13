<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Concert;
use App\Models\BookingSeat;
use App\Models\Booking;

class ConcertController extends Controller
{
    /** 공연 목록 */
    public function index(Request $request, array $params): Response
    {
        $search  = $request->query('search', '');
        $venue   = $request->query('venue', '');
        $order   = $request->query('order', 'desc');

        return $this->view('concerts.index', [
            'title'    => '공연 목록 - TicketBox',
            'concerts' => Concert::getAll($search, $venue, $order),
            'cities'   => Concert::getCities(),
            'search'   => $search,
            'venue'    => $venue,
            'order'    => $order,
        ]);
    }

    /** 공연 상세 */
    public function show(Request $request, array $params): Response
    {
        $concert = Concert::findWithRemaining((int) $params['id']);
        if (!$concert) {
            return new Response('<h1>공연을 찾을 수 없습니다.</h1>', 404);
        }

        return $this->view('concerts.show', [
            'title'   => $concert['title'] . ' - TicketBox',
            'concert' => $concert,
        ]);
    }

    /** 좌석 선택 폼 (GET) */
    public function book(Request $request, array $params): Response
    {
        $concert = Concert::findWithRemaining((int) $params['id']);
        if (!$concert) {
            return new Response('<h1>공연을 찾을 수 없습니다.</h1>', 404);
        }

        // 접근 제한: 비로그인
        if ($guard = $this->requireAuth()) {
            return $guard;
        }

        // 접근 제한: 공연 종료
        if (new \DateTime() > new \DateTime($concert['date'])) {
            $this->setFlash('warning', '이미 종료된 공연입니다.');
            return $this->redirect('/concerts/' . $concert['id']);
        }

        // 접근 제한: 매진
        if ($concert['remaining'] <= 0) {
            $this->setFlash('warning', '매진된 공연입니다.');
            return $this->redirect('/concerts/' . $concert['id']);
        }

        $bookedSeats = BookingSeat::bookedSeats((int) $params['id']);

        return $this->view('concerts.book', [
            'title'       => '티켓 예매 - TicketBox',
            'concert'     => $concert,
            'bookedSeats' => $bookedSeats,
        ]);
    }

    /** 예매 처리 (POST) */
    public function store(Request $request, array $params): Response
    {
        if ($guard = $this->requireAuth()) {
            return $guard;
        }

        $concert = Concert::findWithRemaining((int) $params['id']);
        if (!$concert) {
            return new Response('공연을 찾을 수 없습니다.', 404);
        }

        $seats = $_POST['seats'] ?? [];
        if (empty($seats)) {
            $this->setFlash('warning', '좌석을 선택해주세요.');
            return $this->redirect('/concerts/' . $concert['id'] . '/book');
        }

        $result = Booking::createWithSeats($this->userId(), $concert['id'], $seats);

        if ($result['error']) {
            $this->setFlash('danger', '이미 예매된 좌석입니다: ' . implode(', ', $result['seats']));
            return $this->redirect('/concerts/' . $concert['id'] . '/book');
        }

        return $this->redirect('/bookings?new=' . $result['booking_id']);
    }
}
