<?php

namespace App\Services;

class CartService
{
    public static function addProduct(array $product): bool
    {
        if (!isset($_SESSION['cart_products'])) {
            $_SESSION['cart_products'] = [];
        }
        $cart = $_SESSION['cart_products'];
        $product['cart_quantity'] = 1;
        $product['cart_price'] = $product['price'];
        $search = (string) array_search($product['id'], array_map(function ($i) {
            return $i['id'];
        }, $cart));
        if (strlen($search) > 0) {
            return false;
        } else {
            array_push($_SESSION['cart_products'], $product);
            return true;
        }
    }
    public static function removeProduct($idproduct): bool
    {
        $cart = $_SESSION['cart_products'];
        $product['cart_quantity'] = 1;
        $search = array_search($idproduct, array_map(function ($i) {
            return $i['id'];
        }, $cart));
        $vef = (string) $search;
        if (strlen($vef) > 0) {
            unset($_SESSION['cart_products'][$search]);
            return true;
        } else {
            return false;
        }
    }
    public static function incrementQuantity($idproduct, int $quantity = 1)
    {
        $cart = $_SESSION['cart_products'];
        $search = array_search($idproduct, array_map(function ($i) {
            return $i['id'];
        }, $cart));
        $currentQuantity = (int) $cart[$search]['cart_quantity'];
        $priceProduct = (float) $cart[$search]['price'];
        $newQuantity = $currentQuantity + $quantity;
        $newPrice = $priceProduct * $newQuantity;
        $_SESSION['cart_products'][$search]['cart_quantity'] = $newQuantity;
        $_SESSION['cart_products'][$search]['cart_price'] = $newPrice;
        return true;
    }
    public static function decrementQuantity($idproduct, int $quantity = 1)
    {
        $cart = $_SESSION['cart_products'];
        $search = array_search($idproduct, array_map(function ($i) {
            return $i['id'];
        }, $cart));
        $currentQuantity = (int) $cart[$search]['cart_quantity'];
        $priceProduct = (float) $cart[$search]['price'];
        $newQuantity = $currentQuantity - $quantity;
        $newPrice = $priceProduct * $newQuantity;
        $_SESSION['cart_products'][$search]['cart_quantity'] = $newQuantity;
        $_SESSION['cart_products'][$search]['cart_price'] = $newPrice;
        return true;
    }
    public static function list()
    {
        return (!isset($_SESSION['cart_products'])) ? [] : $_SESSION['cart_products'];
    }
}
