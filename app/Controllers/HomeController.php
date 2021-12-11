<?php

namespace App\Controllers;

use App\Support\Page;

class HomeController extends Page
{
    const TITLE = "Inicio";
    const TEMPLATE = "default";

    public function index()
    {
        return $this->view('Home');
    }
}
