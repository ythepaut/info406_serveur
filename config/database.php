<?php

include_once("../shared/exceptions/DatabaseConnectionException.php");

class Database {

    private $host;
    private $basename;
    private $user;
    private $passwd;

    public $connection;


    public function __construct() {

        require_once("/home/ythepautfc/server/db-config-g4.php");

        $this->host = $db["host"];
        $this->basename = $db["basename"];
        $this->user = $db["user"];
        $this->passwd = $db["passwd"];

        $this->connection = null;

    }


    public function getConnection() {

        $this->connection = mysqli_connect($this->host, $this->passwd, $this->passwd, $this->basename);

        if ($this->connection === false) {
            throw new DatabaseConnectionException("Database connection failed.", 1);
        } else {
            mysqli_set_charset($this->connection, "utf8");
            return $this->connection;
        }

    }


}


?>