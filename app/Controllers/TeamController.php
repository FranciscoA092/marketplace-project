<?php

namespace App\Controllers;

use App\Support\Page;

class TeamController extends Page
{

    const TITLE = "Nossa equipe";

    public function index()
    {
        return $this->view('Team');
    }
}
