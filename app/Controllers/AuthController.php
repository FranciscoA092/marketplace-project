<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Support\Page;

class AuthController extends Page
{
    const TITLE     = "Login";
    const TEMPLATE  = "auth";

    private $service;

    public function __construct()
    {
        //constructor method
        $this->service = new AuthService();
    }

    public function index()
    {
        //return view index fo controller
        return $this->view('Signin');
    }
    public function create()
    {
        return $this->view('Signup');
    }
    /**
     * @method Signin
     * @param input via POST
     * @return view
     */
    public function signin()
    {
        $login = $_POST['login'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($login == null or $password == null) {
            //return error
            return $this->view('Signin', ['message' => 'Informações de login ou senha não encontrado(s).']);
        }
        //first check if login is company
        $action = $this->service->signin($login, md5($password));
        if ($action['status'] == "success") {
            //redirect for home page
        } else {
            return $this->view('Signin', ['message' => $action['message']]);
        }
    }

    public function signup()
    {
        $login = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $password_confirmation = $_POST['password_confirmation'] ?? null;
        $name = $_POST['name'] ?? null;
        $tipo = $_POST['tipo'] ?? null;
        //check values
        if ($login == null or $password == null or $password_confirmation == null or $name == null or $tipo == null) {
            return $this->view('Signup', ['message' => 'Favor preencher todos os campos', 'form' => $_POST]);
        }
        if ($password != $password_confirmation) {
            return $this->view('Signup', ['message' => 'Senha de confirmação divergente', 'form' => $_POST]);
        }
        $tipo = $tipo == 'empresa' ? 1 : 2;
        //continue
        $action = $this->service->signup([
            'name' => $name,
            'email' => $login,
            'password' => md5($password),
            'level' => $tipo
        ]);
        if ($action['status'] == "success") {
            //redirect for home page
        } else {
            return $this->view('Signup', ['message' => $action['message'], 'form' => $_POST]);
        }
    }
}
