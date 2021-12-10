<?php

namespace App\Support;

use App\Support\Contracts\ModelInterface;
use Exception;

class ModelHandler extends DB implements ModelInterface
{
    private $_response;

    public function __construct()
    {
        parent::__construct();
        if (!property_exists($this, 'table')) {
            throw new Exception("Error, property table not found in class model", 1);
        }
        if (!property_exists($this, 'primaryKey')) {
            throw new Exception("Error, property primary key bot found in class model", 1);
        }
    }
    //methods of return response
    public function get()
    {
        return $this->_response ?? [];
    }
    public function first()
    {
        return $this->_response[0] ?? [];
    }
    //methods for manipulation of database
    public function create(array $data): array
    {
        try {
            $columns = array_keys($data);
            $values = array_values($data);

            $columns = implode(",", $columns);
            $values = array_map(function ($i) {
                return "'$i'";
            }, $values);
            $values = implode(",", $values);

            $script = "INSERT INTO " . $this->table . " ($columns) VALUES ($values)";

            $create = $this->_database->query($script);

            return [
                'status' => 'success',
                'response' => $create
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    public function update(array $data, $id): array
    {
        try {
            $object = $this->find($id);
            if (!array_key_exists($this->primaryKey, $object)) {
                return [
                    'status' => 'failed',
                    'message' => 'Não encontrado no banco de dados'
                ];
            }
            //continue
            $columns = array_keys($data);
            $insert = "";

            for ($i = 0; $i < count($columns); $i++) {
                if ($i == count($columns) + 1) {
                    $insert .= "$columns[$i] = '" . $data[$columns[$i]] . "'";
                } else {
                    $insert .= "$columns[$i] = '" . $data[$columns[$i]] . "', ";
                }
            }

            $script = "UPDATE " . $this->table . " SET $insert WHERE " . $this->primaryKey . " = $id";
            $query = $this->_database->query($script);

            return [
                'status' => 'success',
                'message' => 'Atualizado com sucesso'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    public function delete($id): bool
    {
        $object = $this->find($id);
        if (!array_key_exists($this->primaryKey, $object)) {
            return false;
        }
        //continue
        $script = "DELETE FROM " . $this->table . " WHERE " . $this->primaryKey . " = $id";
        $this->_database->query($script);
        return true;
    }
    public function all(): array
    {
        $columns = implode(",", $this->fillable);
        $script = "SELECT $columns FROM " . $this->table;
        $query = $this->_database->query($script);
        if ($query and $query->rowCount() > 0) {
            return $query->fetchAll();
        } else {
            return [];
        }
    }
    public function find($id): array
    {
        $script = "SELECT * FROM " . $this->table . " WHERE " . $this->primaryKey . "='" . $id . "'";
        $query = $this->_database->query($script);
        if ($query and $query->rowCount() == 1) {
            return $query->fetch();
        } else {
            return [];
        }
    }
    public function where(array $clausule): ModelHandler
    {
        $where = "";
        $error = "";

        for ($i = 0; $i < count($clausule); $i++) {
            if (!in_array($clausule[$i][0], $this->fillable)) {
                $error .= "Column '" . $clausule[$i][0] . "' not find in list de columns fillable fo class model";
                break;
            }
            $column = $clausule[$i][0];
            $operation = $clausule[$i][1];
            $value = $clausule[$i][2];

            $where .= "$column $operation '$value' " . ($i == count($clausule) - 1 ? "" : "AND ");
        }

        if ($error != "") {
            throw new Exception("Error, $error", 1);
        }
        //continue
        $columns = implode(",", $this->fillable);
        $script = "SELECT $columns FROM " . $this->table . " WHERE $where";
        $query = $this->_database->query($script);

        if ($query and $query->rowCount() > 0) {
            $datas = $query->fetchAll();
            $this->_response = $datas;
        } else {
            $this->_response = [];
        }

        return $this;
    }
}
