<?php

namespace App\Models;

use App\Support\ModelHandler;

class Product extends ModelHandler
{
    protected $table = "products";
    protected $primaryKey = "id";
    protected $fillable = [
        'id', 'name', 'image', 'description', 'price', 'category', 'quantity', 'id_company', 'link_external'
    ];
}
