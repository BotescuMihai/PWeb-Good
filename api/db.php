<?php

class db
{

    private $dbhost = 'localhost';
    private $dbuser = 'root';
    private $dbpass = 'root';
    private $dbname = 'proiectpw';
    private $conn;

    function __construct()
    { /* ctor. */
        $this->conn = $this->connect();
    }

    private function connect()
    {

        // https://www.php.net/manual/en/pdo.connections.php
        $prepare_conn_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
        $dbConn = new PDO($prepare_conn_str, $this->dbuser, $this->dbpass);

        // https://www.php.net/manual/en/pdo.setattribute.php
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // return the database connection back
        return $dbConn;
    }

    public function getPDOConn()
    {
        return connect();
    }

    public function execute_SELECT($query)
    {
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $this->conn->query($query)->fetchAll();
        return $stmt;
    }


}

;
?>