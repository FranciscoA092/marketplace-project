<?php

namespace App\Services;

use App\Models\User;
use stdClass;

class AuthService
{
    private $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public static function check(): bool
    {
        return isset($_SESSION['auth_email']) and strlen(str_replace(' ', '', $_SESSION['auth_email'])) > 0 ? true : false;
    }

    public function signin(string $login, string $password): array
    {
        $user = $this->model->where([
            ['email', '=', $login],
            ['password', '=', $password]
        ])->first();

        if (!$user) {
            return [
                'status' => 'failed', 'message' => 'Login ou Senha incorreto(s).'
            ];
        }
        //continue
        $_SESSION['auth_email'] = $user['email'];
        $_SESSION['auth_name']  = $user['name'];
        $_SESSION['auth_level'] = $user['level'];
        $_SESSION['auth_userid'] = base64_encode($user['id']);

        return [
            'status' => 'success', 'message' => 'Login efetuado com sucesso'
        ];
    }

    public function signup(array $data): array
    {
        $create_user = $this->model->create($data);
        if ($create_user['status'] != "success") {
            return $create_user;
        }
        //continue
        $signin = $this->signin($data['email'], $data['password']);
        return $signin;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_email']);
        unset($_SESSION['auth_name']);
        unset($_SESSION['auth_level']);
        unset($_SESSION['auth_userid']);
        session_destroy();
    }
}
