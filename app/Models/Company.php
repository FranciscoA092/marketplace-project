<?php

namespace App\Models;

use App\Support\ModelHandler;

class Company extends ModelHandler
{
    protected $table = 'company';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'cnpj', 'name', 'email', 'password', 'cep'
    ];
}
