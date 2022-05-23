<?php
session_start();

class db
{
    /*
     $_SESSION['HOST'] = $_POST['dbhost'];
    $_SESSION['dbname'] = $_POST['dbname'];
    $_SESSION['USER'] = $_POST['dbuser'];
    $_SESSION['DB_password'] = $_POST['dbpassword'];
    $_SESSION['PDO'] = new PDO('sqlite: ./pw.db');
     */
    private $dbhost = 'localhost';
    private $dbuser = 'root';
    private $dbpass = 'root';
    private $dbname = 'proiectpw';
    private $conn;
    private $pdo;

    function __construct()
    { /* ctor. */
        if (isset($_SESSION['HOST']) && isset($_SESSION['dbname']) && isset($_SESSION['USER']) && isset($_SESSION['DB_password']) && isset($_SESSION['PDO']))
            if ($_SESSION['PDO'] == 'sqlite3') {
                $this->dbhost = $_SESSION['HOST'];
                $this->dbuser = $_SESSION['USER'];
                $this->dbpass = $_SESSION['DB_password'];
                $this->dbname = $_SESSION['dbname'];
                $this->pdo = new PDO('sqlite:' . __DIR__ . '/pw.db');
                //    echo 'sqlite:' . __DIR__ . 'pw.db';
                //else $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass);
            } else $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass);
        else  $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbuser, $this->dbpass);
        $this->conn = $this->connect();
        // print_r($this->pdo);
    }

    private function connect()
    {

        // https://www.php.net/manual/en/pdo.connections.php
        // $prepare_conn_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
        //  $dbConn = new PDO($prepare_conn_str, $this->dbuser, $this->dbpass);
        // $dbConn = $pdo
        $dbConn = $this->pdo;
        // https://www.php.net/manual/en/pdo.setattribute.php
        // $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // return the database connection back
        return $dbConn;
    }

    public function getPDOConn()
    {
        return connect();
    }

    public function execute_SELECT($query)
    {
        // $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $this->conn->query($query)->fetchAll();
        return $stmt;
    }

    public function getPDO()
    {
        return $this->pdo;
    }


}

//$db = new db();
//print_r($db->getPDO());

;
?>