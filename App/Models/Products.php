<?php

namespace App\Models;

use App\Core\Database;
use PDO;

interface IProducts
{
    public function getAll();
    public function getByID(int $id);
    public function getByURI(string $uri);
    public function getStockByID(int $id, int $quantity);
    public function getStockByURI(string $uri, int $quantity);
}

class Products implements IProducts
{
    /**
     * Consulta todos os produtos
     *
     * @return void
     */
    public function getAll()
    {
        $pdo = new Database();
        $stmt = $pdo->executeQuery("SELECT * FROM tbl_produtos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta produto pelo ID
     *
     * @param integer $id
     * @return void
     */
    public function getByID(int $id)
    {
        $pdo = new Database();
        $stmt = $pdo->executeQuery("SELECT * FROM tbl_produtos WHERE id= :ID", array(":ID" => $id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta produto pelo URI
     *
     * @param string $uri
     * @return void
     */
    public function getByURI(string $uri)
    {
        $pdo = new Database();
        $stmt = $pdo->executeQuery("SELECT * FROM tbl_produtos WHERE uri= :URI", array(":URI" => $uri));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta estoque do produto pelo ID
     *
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function getStockByID(int $id, int $quantity)
    {
        $pdo = new Database();
        $stmt = $pdo->executeQuery("SELECT * FROM tbl_produtos id= :ID AND quantity= >= :QUANTITY", array(":ID" => $id, ":QUANTITY" => $quantity));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Consulta estoque do produto pela URI
     *
     * @param string $uri
     * @param integer $quantity
     * @return void
     */
    public function getStockByURI(string $uri, int $quantity)
    {
        $pdo = new Database();
        $stmt = $pdo->executeQuery("SELECT * FROM tbl_produtos uri= :URI AND quantity= >= :QUANTITY", array(":ID" => $uri, ":QUANTITY" => $quantity));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
