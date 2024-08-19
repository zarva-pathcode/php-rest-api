<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $password;
    public $conn;

    public function __construct($config)
    {
        $this->host = $config['db']['host'];
        $this->dbname = $config['db']['dbname'];
        $this->user = $config['db']['user'];
        $this->password = $config['db']['password'];
    }

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
