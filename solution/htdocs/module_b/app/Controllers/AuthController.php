<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\User;

class AuthController extends Controller
{
    public function registerForm(Request $request, array $params): Response
    {
        return $this->view('auth.register', ['title' => '회원가입 - TicketBox']);
    }

    public function register(Request $request, array $params): Response
    {
        $name     = trim($request->input('name', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $phone    = trim($request->input('phone', ''));
        $errors   = [];

        // 유효성 검사
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = '이메일 형식이 올바르지 않습니다.';
        } elseif (User::emailExists($email)) {
            $errors[] = '이미 사용 중인 이메일입니다.';
        }
        if (mb_strlen($password) < 4) {
            $errors[] = '비밀번호는 4자 이상이어야 합니다.';
        }
        if (!preg_match('/^\d+$/', $phone)) {
            $errors[] = '전화번호는 숫자만 입력하세요.';
        }

        if ($errors) {
            return $this->view('auth.register', [
                'title'  => '회원가입 - TicketBox',
                'errors' => $errors,
                'old'    => compact('name', 'email', 'phone'),
            ]);
        }

        $id = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone'    => $phone,
        ]);

        // 가입 후 자동 로그인
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $name;

        return $this->redirect('/');
    }

    public function loginForm(Request $request, array $params): Response
    {
        return $this->view('auth.login', ['title' => '로그인 - TicketBox']);
    }

    public function login(Request $request, array $params): Response
    {
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->view('auth.login', [
                'title' => '로그인 - TicketBox',
                'error' => '이메일 또는 비밀번호가 올바르지 않습니다.',
                'old'   => ['email' => $email],
            ]);
        }

        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];

        return $this->redirect('/');
    }

    public function logout(Request $request, array $params): Response
    {
        session_destroy();
        return $this->redirect('/');
    }
}
