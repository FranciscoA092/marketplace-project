<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\StorageService;
use App\Support\Page;

class ProductController extends Page
{

    const TITLE = "Produtos";

    private $model;
    private $user_level;

    public function __construct()
    {
        $this->model = new Product();
        $this->user_level = auth()->level;
    }

    public function index()
    {
        $list = []; //list of products
        switch ($this->user_level) {
            case 2:
                $list = $this->model->all();
                break;

            default:
                $list = [];
                break;
        }

        return $this->view('Products.List', ['data' => $list]);
    }

    public function create()
    {
        return $this->view('Products.Create');
    }

    public function store()
    {
        //method access vie request post ajax
        if (!empty($_POST)) {
            $file = $_FILES['file'] ?? null;
            $name = $_POST['name'] ?? null;
            $category = $_POST['category'] ?? null;
            $price = $_POST['price'] ?? null;
            $description = $_POST['description'] ?? null;
            //check
            if ($file == null or $name == null or $category == null or $price == null or $description == null) {
                return response([
                    'status' => 'failed',
                    'message' => 'Todos os campos são obrigatorios!',
                    'post_receipt' => $file
                ], 400);
            }
            //continue
            $newNameFile = md5(date('YmdHis'));
            if (StorageService::put($file, 'products', $newNameFile)) {
                //file of image saved
                $data_save = [
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'description' => $description,
                    'image' => $newNameFile
                ];
                $save = $this->model->create($data_save);
                if ($save['status'] == 'success') {
                    return response([
                        'status' => "success",
                        'message' => 'Produto cadastrado com sucesso'
                    ], 200);
                } else {
                    return response([
                        'status' => 'error',
                        'message' => $save['message']
                    ], 400);
                }
            } else {
                return response([
                    'status' => 'error',
                    'message' => 'Não foi possivel salvar imagem do produto!'
                ], 400);
            }
        }
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
        //method access via request post ajax
    }
}
