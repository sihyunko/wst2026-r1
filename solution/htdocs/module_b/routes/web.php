<?php

$router = $app->getRouter();

// 공연
$router->get('/',                         'ConcertController@index');
$router->get('/concerts/{id}',            'ConcertController@show');
$router->get('/concerts/{id}/book',       'ConcertController@book');
$router->post('/concerts/{id}/book',      'ConcertController@store');

// 예매 내역
$router->get('/bookings',                 'BookingController@index');
$router->get('/bookings/{id}',            'BookingController@show');
$router->post('/bookings/{id}/cancel',    'BookingController@cancel');

// 인증
$router->get('/register',                 'AuthController@registerForm');
$router->post('/register',                'AuthController@register');
$router->get('/login',                    'AuthController@loginForm');
$router->post('/login',                   'AuthController@login');
$router->get('/logout',                   'AuthController@logout');
