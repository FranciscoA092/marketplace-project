<?php

namespace App\Controllers;

use App\Models\Company;
use App\Support\Page;

class CompanyController extends Page
{

    const TITLE = "Minha empresa";

    private $model;
    private $company;

    public function __construct()
    {
        $this->model = new Company();
        $this->company = $this->model->find(auth()->idCompany);
    }

    public function index()
    {
        if (auth()->check and auth()->level == 1) {

            return $this->view('Company', ['data' => $this->company]);
        } else {
            return $this->redirect(url(['page' => 'home']));
        }
    }

    public function update()
    {
        if (!empty($_POST)) {
            $name = $_POST['nome'] ?? null;
            $cnpj = $_POST['cnpj'] ?? null;
            $cep = $_POST['cep'] ?? null;
            $email = $_POST['email'] ?? null;
            $idCompany = auth()->idCompany;

            if ($name == null or $cnpj == null or $cep == null or $email == null) {
                return $this->view('Company', ['data' => $this->company, 'message' => 'Todos os campos são obrigatorios', 'status' => 'info']);
            }

            $action = $this->model->update([
                'name' => $name,
                'cnpj' => $cnpj,
                'email' => $email,
                'cep' => $cep
            ], $idCompany);

            if ($action['status'] == 'success') {
                return $this->view('Company', ['data' => $this->company, 'message' => 'Dados atualizado com sucess', 'status' => 'success']);
            } else {
                return $this->view('Company', ['data' => $this->company, 'message' => 'Dados atualizado com sucess', 'status' => 'error']);
            }
        } else {
            return $this->view('Notfound');
        }
    }
}
