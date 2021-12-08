<?php

namespace App\Support;

use PDO;
use PDOException;

/**
 * Class of connection with database
 */
class DB
{
    const DB_USER = '';
    const DB_PWD = '';
    const DB_HOST = '';
    const DATABASE = '';

    protected $_database;

    public function __construct()
    {
        $this->_database = $this->connect();
    }

    protected function connect()
    {
        try {
            $conn = new PDO('mysql:host=' . self::DB_HOST . ';dbname=' . self::DATABASE, self::DB_USER, self::DB_PWD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
