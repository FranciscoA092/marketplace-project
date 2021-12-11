<?php
function auth(): stdClass
{
    $data = new stdClass();
    $data->name = $_SESSION['auth_name'];
    $data->email = $_SESSION['auth_email'];
    $data->level = $_SESSION['auth_level'];
    $data->id = base64_decode($_SESSION['auth_userid']);
    if ($_SESSION['auth_level'] == 1) {
        $data->idCompany = base64_decode($_SESSION['auth_company_id']);
        $data->nameCompany = $_SESSION['auth_company_name'];
    }
    return $data;
}

function url(array $data): string
{
    $page = $data['page'] ?? null;
    $action = $data['go'] ?? 'index';
    return BASEURL . ($page != null ? "?page=$page&go=$action" : "?go=$action");
}

function response(array $data, int $status_code)
{
    http_response_code($status_code);
    echo json_encode($data);
}
