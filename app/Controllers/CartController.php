<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\SaleService;
use App\Support\Page;

class CartController extends Page
{

    const TITLE = "Carrinho de compras";

    private $modelProduct;
    private $service;

    public function __construct()
    {
        $this->modelProduct = new Product();
        $this->service = new SaleService();
    }

    public function index()
    {
        $cart = CartService::list();
        return $this->view('Cart', ['data' => $cart]);
    }

    public function confirmed()
    {
        if (isset($_GET['product'])) {
            $id = $_GET['product'];
            $myCart = CartService::list();
            $product = $this->modelProduct->find($id);

            if (count($myCart) > 0) {
                //if exist cart shopping
                $index = array_search($id, array_map(function ($item) {
                    return $item['id'];
                }, $myCart));
                $vef = (string) $index;
                if (strlen($vef) > 0) {
                    $product_my_cart = $myCart[$index];
                    $cart_simulated = array(0 => $product_my_cart);
                    $sale = [
                        'method_payment' => 'external',
                        'total' => $product['price'],
                        'id_user' => auth()->id,
                        'products' => $cart_simulated
                    ];
                    $save = $this->service->sale($sale);
                    if ($save['status'] == 'success') {
                        CartService::removeProduct($id);
                        return $this->view('Payment', ['message' => 'Pagamento aprovado, obrigado por ter comprado <b>' . $product['name'] . '</b>', 'status' => 'success']);
                    } else {
                        return $this->view('Payment', ['message' => 'Não foi possivel confirmar o pagamento em nossa plataforma :(', 'status' => 'error']);
                    }
                } else {
                    $temp = $product;
                    $temp['cart_quantity'] = 1;
                    $temp['cart_price'] = $product['price'];
                    $cart_simulated = array(0 => $temp);
                    $sale = [
                        'method_payment' => 'external',
                        'total' => $product['price'],
                        'id_user' => auth()->id,
                        'products' => $cart_simulated
                    ];
                    $save = $this->service->sale($sale);
                    if ($save['status'] == 'success') {
                        return $this->view('Payment', ['message' => 'Pagamento aprovado, obrigado por ter comprado <b>' . $product['name'] . '</b>', 'status' => 'success']);
                    } else {
                        return $this->view('Payment', ['message' => 'Não foi possivel confirmar o pagamento em nossa plataforma :(', 'status' => 'error']);
                    }
                }
            } else {
                //create a sale with only this product
                $temp = $product;
                $temp['cart_quantity'] = 1;
                $temp['cart_price'] = $product['price'];
                $cart_simulated = array(0 => $temp);
                $sale = [
                    'method_payment' => 'external',
                    'total' => $product['price'],
                    'id_user' => auth()->id,
                    'products' => $cart_simulated
                ];
                $save = $this->service->sale($sale);
                if ($save['status'] == 'success') {
                    return $this->view('Payment', ['message' => 'Pagamento aprovado, obrigado por ter comprado <b>' . $product['name'] . '</b>', 'status' => 'success']);
                } else {
                    return $this->view('Payment', ['message' => 'Não foi possivel confirmar o pagamento em nossa plataforma :(', 'status' => 'error']);
                }
            }
        } else {
            return $this->view('Notfound', ['message' => 'Produto não encontrado']);
        }
    }

    public function sale()
    {
        if (!empty($_POST)) {
            $methods = ['card-credit', 'card-debit', 'boleto'];
            $method_payment = $_POST['method_payment'] ?? null;
            if ($method_payment == null or !in_array($method_payment, $methods)) {
                return response([
                    'status' => 'error',
                    'message' => 'Forma de pagamento não encontrada ou não suportada'
                ], 400);
            }
            //continue
            $products_cart = CartService::list();
            $total = array_sum(array_map(function ($item) {
                return $item['cart_price'];
            }, $products_cart));
            $data_sale = [
                'method_payment' => $method_payment,
                'total' => $total,
                'id_user' => auth()->id,
                'products' => $products_cart
            ];

            $save = $this->service->sale($data_sale);
            if ($save['status'] == 'success') {
                //clear cart
                $_SESSION['cart_products'] = [];
                return response($save, 200);
            } else {
                return response($save, 400);
            }
        }
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
            $increment = CartService::incrementQuantity($idProduct);
            if ($increment['status'] == 'success') {
                return response(['status' => 'success', 'message' => 'Quantidade incrementada'], 200);
            } else {
                return response($increment, 400);
            }
        }
    }

    public function decrement()
    {
        if (!empty($_POST)) {
            $idProduct = $_POST['id_product'] ?? null;
            if ($idProduct == null) {
                return response(['status' => 'failed', 'message' => 'Produto nao encontrado'], 400);
            }
            $decrement = CartService::decrementQuantity($idProduct);
            if ($decrement['status'] == 'success') {
                return response(['status' => 'success', 'message' => 'Quantidade atualizada'], 200);
            } else {
                return response($decrement, 400);
            }
        }
    }
}
