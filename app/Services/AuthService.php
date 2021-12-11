<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use stdClass;

class AuthService
{
    private $model;
    private $modelCompany;

    public function __construct()
    {
        $this->model = new User();
        $this->modelCompany = new Company();
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
        //if level == 1 of company
        if ($user['level'] == 1) {
            $company = $this->modelCompany->where([['id_user', '=', $user['id']]])->first();
            $_SESSION['auth_company_id'] = base64_encode($company['id']);
            $_SESSION['auth_company_name'] = $company['name'];
        }

        return [
            'status' => 'success', 'message' => 'Login efetuado com sucesso'
        ];
    }

    public function signup(array $data, array $company = []): array
    {
        $create_user = $this->model->create($data);
        if ($create_user['status'] != "success") {
            return $create_user;
        }
        if ($data['level'] == 1) {
            //create company
            $company['id_user'] = $create_user['id'];
            $company['email'] = $data['email'];
            $create_company = $this->modelCompany->create($company);
            if ($create_company['status'] != "success") {
                return $create_company;
            }
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
