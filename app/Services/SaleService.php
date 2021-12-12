<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Support\DB;
use Exception;

class SaleService extends DB
{
    private $modelSale;
    private $modelSaleProduct;
    private $modelProduct;

    public function __construct()
    {
        parent::__construct();
        $this->modelSale = new Sale();
        $this->modelSaleProduct = new SaleProduct();
        $this->modelProduct = new Product();
    }

    public function reportingMySales($idcompany): array
    {
        $products = $this->modelProduct->where([['id_company', '=', auth()->idCompany]])->get();
        $productsStringId = implode(',', array_map(function ($i) {
            return $i['id'];
        }, $products));
        $salesProducts = $this->modelSaleProduct->where([
            ['id_product', 'IN', "($productsStringId)"]
        ])->get();

        $data = [];

        foreach ($salesProducts as $sale) {
            $indexProduct = array_search($sale['id_product'], array_map(function ($p) {
                return $p['id'];
            }, $products));
            array_push($data, [
                'id' => $sale['id'],
                'total' => $sale['total'],
                'quantity' => $sale['quantity'],
                'id_product' => $sale['id_product'],
                'category' => $products[$indexProduct]['category'],
                'name' => $products[$indexProduct]['name'],
                'image' => $products[$indexProduct]['image']
            ]);
        }
        return $data;
    }

    public function sale(array $data): array
    {
        $this->_database->beginTransaction();
        try {

            $sale = [
                'method_payment' => $data['method_payment'],
                'total' => $data['total'],
                'id_user' => $data['id_user']
            ];

            $sale_products = array_map(function ($item) {
                return [
                    'total' => $item['cart_price'],
                    'quantity' => $item['cart_quantity'],
                    'id_product' => $item['id']
                ];
            }, $data['products']);

            $save_sale = $this->modelSale->create($sale);
            if ($save_sale['status'] != 'success') {
                return $save_sale;
            }
            //continue
            foreach ($sale_products as $save) {
                $save['id_sale'] = $save_sale['id'];
                $save_product = $this->modelSaleProduct->create($save);
            }

            $this->_database->commit();
            return [
                'status' => 'success',
                'message' => 'Compra concluida com sucesso'
            ];
        } catch (Exception $e) {
            $this->_database->rollBack();
            return [
                'status' => "error",
                'message' => $e->getMessage()
            ];
        }
    }
}
