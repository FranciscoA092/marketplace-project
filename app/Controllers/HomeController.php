<?php

namespace App\Controllers;

use App\Models\Product;
use App\Support\Page;

class HomeController extends Page
{
    const TITLE = "Inicio";
    const TEMPLATE = "default";

    private $modelProduct;

    public function __construct()
    {
        $this->modelProduct = new Product();
    }

    public function index()
    {
        $products = array_slice($this->modelProduct->all(), 0, 8);
        return $this->view('Home', ['products' => $products]);
    }
}
