<?php

namespace App\Models;

use App\Support\ModelHandler;

class Sale extends ModelHandler
{
    protected $table = "sales";
    protected $primaryKey = "id";
    protected $fillable = [
        'id', 'method_payment', 'total', 'data_sale', 'id_user'
    ];
}
