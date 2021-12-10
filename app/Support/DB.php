<?php

namespace App\Support;

use PDO;
use PDOException;

/**
 * Class of connection with database
 */
class DB
{
    protected $_database;

    public function __construct()
    {
        $this->_database = $this->connect();
    }

    protected function connect()
    {
        try {
            $conn = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DATABASE, DB_USER, DB_PWD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
