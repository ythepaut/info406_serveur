<?php

class User {

    private $connection;
    private $tableName = "g4_user";

    private $id;
    private $username;
    private $passwd;

    /**
     * Constructeur de la classe utilisateur
     * 
     * @param mysqlconnection           $connection         -   Connexion à la base de donnée
     */
    public function __construct($connection) {
        $this->connection = $connection;
    }


    public function getUsername() {
        return $this->username;
    }

    public function getPasswd() {
        return $this->passwd;
    }


    public function setUsername($username) {
        $this->username = htmlspecialchars(strip_tags($username));
    }

    public function setPasswd($passwd) {
        $this->passwd = htmlspecialchars(strip_tags($passwd));
    }

    /**
     * Enregistrer l'utilisateur dans la base
     * 
     * @return boolean
     */
    public function register() {
        
        //TODO : Verification duplication noms d'utilisateurs etc...

        $salt = randomString(16);
        $password_salted_hashed = password_hash(hash('sha512', $passwd . $salt), PASSWORD_DEFAULT, ['cost' => 12]);

        $query = $connection->prepare("INSERT INTO ? (username, passwd) VALUES (?, ?)");
        $query->bind_param("sss", $this->tableName, $this->username, $this->passwd);
        return $query->execute();

    }

    /**
     * Methode qui retourne vrai si le nom d'utilisateur existe
     * Note : Mettre en static ?
     * 
     * @param string                    $username           -   Nom d'utilisateur à vérifier
     * 
     * @return bool
     */
    public function usernameExists($username) : bool {

        return true;

    }

}



?>