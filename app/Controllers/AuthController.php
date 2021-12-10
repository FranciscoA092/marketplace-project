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
        $action = $this->service->signin($login, $password);
        if ($action['status'] == "success") {
            //redirect for home page
        } else {
            return $this->view('Signin', ['message' => $action['message']]);
        }
    }
}
