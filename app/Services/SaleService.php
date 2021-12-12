<?php

namespace App\Services;

use App\Models\Company;
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
    private $modelCompany;

    public function __construct()
    {
        parent::__construct();
        $this->modelSale = new Sale();
        $this->modelSaleProduct = new SaleProduct();
        $this->modelProduct = new Product();
        $this->modelCompany = new Company();
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

    public function reportDashboard(): array
    {
        $sales = $this->modelSale->all();
        $sales_products = $this->modelSaleProduct->all();
        $products = $this->modelProduct->all();
        $companies = $this->modelCompany->all();
        $rank = $this->modelSale->sqlQuery("SELECT * FROM total_sales_products ORDER BY total_quantity DESC")->get();

        $totalSalesQuantity = array_sum(array_map(function ($i) {
            return $i['quantity'];
        }, $sales_products));
        $totalSalesMoney = array_sum(array_map(function ($i) {
            return $i['total'];
        }, $sales));

        $data = [
            'count_companies' => count($companies),
            'count_products' => count($products),
            'count_sales' => count($sales),
            'total_money_sales' => $totalSalesMoney,
            'total_quantity_sales' => $totalSalesQuantity,
            'companies' => $companies,
            'rank' => array_slice($rank, 0, 5)
        ];

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
