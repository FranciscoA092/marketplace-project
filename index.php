<?php
//file index central fo system
setlocale(LC_MONETARY, 'pt_BR');
ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/conf.php';
require __DIR__ . '/helpers.php';

session_start();

$controller = isset($_GET['page']) ? $_GET['page'] : 'home';
$method = isset($_GET['go']) ? $_GET['go'] : 'index';

//run controler and method
$controller = "App\Controllers\\" . ucfirst($controller) . "Controller";

if(!class_exists($controller)){
  $controller = "App\Controllers\NotfoundController.php";
  $method = 'index';
}
//instance
$app = new $controller;
$app->$method();
