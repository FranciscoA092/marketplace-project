<?php

namespace App\Models;

use App\Support\ModelHandler;

class Client extends ModelHandler
{
    protected $table = "client";
    protected $primaryKey = "id";

    protected $fillable = [
        'id', 'name'
    ];
}
