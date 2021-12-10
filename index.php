<?php
//file index central fo system

use App\Services\AuthService;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/conf.php';

session_start();

$controller = isset($_GET['page']) ? $_GET['page'] : 'home';
$method = isset($_GET['go']) ? $_GET['go'] : 'index';

//check login session
if (!AuthService::check()) {
    $controller = 'auth';
}

//run controler and method
$controller = "App\Controllers\\" . ucfirst($controller) . "Controller";
//instance
$app = new $controller;
$app->$method();
