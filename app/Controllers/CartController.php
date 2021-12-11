<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Support\Page;

class CartController extends Page
{

    const TITLE = "Carrinho de compras";

    private $modelProduct;

    public function __construct()
    {
        $this->modelProduct = new Product();
    }

    public function index()
    {
        $cart = CartService::list();
        return $this->view('Cart', ['data' => $cart]);
    }

    public function add()
    {
        if (!empty($_POST)) {
            $idProduct = $_POST['id_product'] ?? null;
            if ($idProduct == null) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }
            //continue
            $product = $this->modelProduct->find($idProduct);
            if (!$product) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }

            $add = CartService::addProduct($product);
            if (!$add) {
                return response(['status' => 'failed', 'message' => 'Este produto já está no seu carrinho'], 400);
            }
            return response(['status' => 'success', 'message' => 'Produto adicionado ao carrinho'], 200);
        }
    }

    public function remove()
    {
        if (!empty($_POST)) {
            $idProduct = $_POST['id_product'] ?? null;
            if ($idProduct == null) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }
            $remove = CartService::removeProduct($idProduct);
            return response(['status' => 'success', 'message' => 'Produto removido do carrinho'], 200);
        }
    }


    public function increment()
    {
        if (!empty($_POST)) {
            $idProduct = $_POST['id_product'] ?? null;
            if ($idProduct == null) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }
            $remove = CartService::incrementQuantity($idProduct);
            return response(['status' => 'success', 'message' => 'Quantidade incrementada'], 200);
        }
    }

    public function decrement()
    {
        if (!empty($_POST)) {
            $idProduct = $_POST['id_product'] ?? null;
            if ($idProduct == null) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }
            $remove = CartService::decrementQuantity($idProduct);
            return response(['status' => 'success', 'message' => 'Quantidade atualizada'], 200);
        }
    }
}
