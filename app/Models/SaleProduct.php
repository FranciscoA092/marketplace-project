<?php

namespace App\Models;

use App\Support\ModelHandler;

class SaleProduct extends ModelHandler
{
    protected $table = "sale_product";
    protected $primaryKey = "id";
    protected $fillable = [
        'id', 'total', 'quantity', 'id_sale', 'id_product'
    ];
}
