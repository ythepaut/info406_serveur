<?php

/**
 * Classe (Singleton) qui gère la connexion à la base de données
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Database {

    private static $instance = null;
    private $host;
    private $basename;
    private $user;
    private $passwd;

    private $connection;


    public function __construct() {

        //Importation des identifiants de la base
        require_once("/home/ythepautfc/server/db-config-g4.php");

        $this->host = $db["host"];
        $this->basename = $db["basename"];
        $this->user = $db["user"];
        $this->passwd = $db["passwd"];

        $this->connection = null;

        $this->connect();

    }


    /**
     * Getteur de l'instance
     */
    public static function getInstance() : self {

        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;

    }


    /**
     * Methode qui crée une connexion à la base de donnée.
     * 
     * @return void
     * 
     * @throws DatabaseConnectionException   Connexion à la base échouée
     */
    private function connect() : void {
        
        $this->connection = mysqli_connect($this->host, $this->user, $this->passwd, $this->basename);

        if ($this->connection === false) {
            throw new DatabaseConnectionException("Database connection failed.", 1);
        } else {
            mysqli_set_charset($this->connection, "utf8");
        }

    }


    public function getConnection() : object {
        return $this->connection;
    }



}


?>