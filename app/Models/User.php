<?php

namespace App\Models;

use App\Support\ModelHandler;

class User extends ModelHandler
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $fillable = [
        "id", "name", "email", "password", "level"
    ];
}
