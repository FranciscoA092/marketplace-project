<?php

namespace App\Controllers;

use App\Services\SaleService;
use App\Support\Page;

class ReportController extends Page
{

    const TITLE = "Relatórios";

    private $serviceSale;

    public function __construct()
    {
        $this->serviceSale = new SaleService();
    }

    public function index()
    {
        if (auth()->level == 1) {
            $data = $this->serviceSale->reportingMySales(auth()->idCompany);
            return $this->view('Reports.SaleCompany', ['data' => $data]);
        }
    }
}
