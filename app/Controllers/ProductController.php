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
            case 1:
                $list = $this->model->where([['id_company', '=', auth()->idCompany]])->get();
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
            $quantity = $_POST['quantity'] ?? 0;
            //check
            if ($file == null or $name == null or $category == null or $price == null or $description == null) {
                return response([
                    'status' => 'failed',
                    'message' => 'Todos os campos são obrigatorios!',
                    'post_receipt' => $file
                ], 400);
            }
            //continue
            if ($upload = StorageService::put($file, 'products')) {
                //file of image saved
                $data_save = [
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'description' => $description,
                    'image' => $upload,
                    'id_company' => auth()->idCompany,
                    'quantity' => $quantity
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

    public function edit()
    {
        $id_product = $_GET['id'] ?? null;
        if ($id_product == null) {
            return $this->view('Notfound');
        }
        $product = $this->model->find($id_product);
        if (!$product) {
            return $this->view('Notfound', ['message' => 'Produto não encontrado']);
        }
        //continue
        return $this->view('Products.Edit', ['product' => $product]);
    }

    public function update()
    {
        //method access vie request post ajax
        if (!empty($_POST)) {
            $id = $_GET['id'] ?? null;
            $name = $_POST['name'] ?? null;
            $category = $_POST['category'] ?? null;
            $price = $_POST['price'] ?? null;
            $description = $_POST['description'] ?? null;
            $quantity = $_POST['quantity'] ?? 0;
            //check
            if ($id == null or $name == null or $category == null or $price == null or $description == null) {
                return response([
                    'status' => 'failed',
                    'message' => 'Todos os campos são obrigatorios!',
                ], 400);
            }
            //continue
            //file of image saved
            $data_save = [
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'description' => $description,
                'quantity' => $quantity
            ];
            $save = $this->model->update($data_save, $id);
            if ($save['status'] == 'success') {
                return response([
                    'status' => "success",
                    'message' => 'Produto atualizado com sucesso'
                ], 200);
            } else {
                return response([
                    'status' => 'error',
                    'message' => $save['message']
                ], 400);
            }
        }
    }

    public function delete()
    {
        if (!empty($_POST)) {
            $id = $_POST['id'] ?? null;
            if ($id == null) {
                return response(['status' => "failed", 'message' => 'Produto não encontrado'], 400);
            }
            $product = $this->model->find($id);
            //continue
            $action = $this->model->delete($id);
            if ($action) {
                StorageService::delete(BASEDIR . '/storage/products/' . $product['image']);
                return response(['status' => "success", 'message' => 'Produto excluido com sucesso'], 200);
            } else {
                return response(['status' => "failed", 'message' => 'Erro ao tentar excluir produto favor tentar novamente'], 400);
            }
        }
    }
}
