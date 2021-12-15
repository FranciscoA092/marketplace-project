<?php
namespace App\Controllers;

use App\Support\Page;

class NotfoundController extends Page
{

    const TITLE = "Carrinho de compras";
    
    public function index(){
      return $this->view('Notfound');
    }
    
}
