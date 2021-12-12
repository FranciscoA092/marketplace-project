<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleProduct;
use App\Support\DB;
use Exception;

class SaleService extends DB
{
    private $modelSale;
    private $modelSaleProduct;

    public function __construct()
    {
        parent::__construct();
        $this->modelSale = new Sale();
        $this->modelSaleProduct = new SaleProduct();
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
