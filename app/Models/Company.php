<?php

namespace App\Models;

use App\Support\ModelHandler;

class Company extends ModelHandler
{
    protected $table = 'companies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'cnpj', 'name', 'email', 'password', 'cep'
    ];
}
