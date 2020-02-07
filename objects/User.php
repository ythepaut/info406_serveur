<?php

class User {

    const TABLE_NAME = "g4_user";

    private $id;
    private $username;
    private $passwd;


    /**
     * Constructeur de la classe utilisateur.
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * @param string                    $username           -   Nom d'utilisateur
     * @param string                    $passwd             -   Mot de passe de l'utilisateur
     * 
     * @return void
     */
    public function __construct($id, $username, $passwd) {
        $this->id = $id;
        $this->username = $username;
        $this->passwd = $passwd;
    }



    /**
     * Fabrique de la classe utilisateur à partir de l'ID.
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * 
     * @return self
     */
    public static function createByID(int $id) : self {
        
        $db = new Database();

        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        return new self($userData['id'], $userData['username'], $userData['passwd']);
    }


    /**
     * Fabrique de la classe utilisateur à partir des identifiants
     * 
     * @param string                    $username           -   Nom d'utilisateur
     * @param string                    $passwd             -   Mot de passe de l'utilisateur
     * 
     * @return self
     */
    public static function createByCredentials(string $username, string $passwd) : self {
        
        $pwdHash = $passwd;//TODO Hash

        $db = new Database();

        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE username = ? AND passwd = ?");
        $query->bind_param("ss", $username, $pwdHash);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        return new self($userData['id'], $userData['username'], $userData['passwd']);
    }


    /**
     * Getter de id
     * 
     * @return int/null
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getter du nom d'utilisateur
     * 
     * @return int/null
     */
    public function getUsername() {
        return $this->username;
    }


}



?>