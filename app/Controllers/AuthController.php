<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Company;

class AuthController
{
    private $modelCompany;
    private $modelClient;
    private $_title = "Login";

    public function __construct()
    {
        $this->modelCompany = new Company();
        $this->modelClient  = new Client();
    }

    public function index()
    {
        //return view index fo controller
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
        }
        //first check if login is company
        $authCompany = $this->modelCompany->where([
            ['email', '=', $login],
            ['password', '=', md5($password)]
        ]);
    }
}
