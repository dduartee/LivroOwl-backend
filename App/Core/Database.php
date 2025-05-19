<?php

namespace App\Core;

use PDO;

class Database extends PDO
{
    private String $DB_NAME = 'framework_db';
    private string $DB_USER = 'root';
    private string $DB_PASS = '';
    private string $DB_HOST = 'localhost';
    private int $DB_PORT = 3306;


    public function __construct()
    {
        $this->conn = new PDO("mysql:dbname=$this->DB_NAME;host=$this->DB_HOST;port=$this->DB_PORT;user=$this->DB_USER;password=$this->DB_PASS");
    }

    private function setParameters($stmt, $key, $value)
    {
        $stmt->bindParam($key, $value);
    }

    private function mountQuery($stmt, $parameters)
    {
        foreach( $parameters as $key => $value ) {
            $this->setParameters($stmt, $key, $value);
        }
    }

    public function executeQuery(string $query, array $parameters = [])
    {
        $stmt = $this->conn->prepare($query);
        $this->mountQuery($stmt, $parameters);
        $stmt->execute();
        return $stmt;
    }
}