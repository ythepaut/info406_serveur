<?php

class User {

    const TABLE_NAME = "g4_user";

    private $id;
    private $username;
    private $email;
    private $status;


    /**
     * Constructeur de la classe utilisateur.
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * @param string                    $username           -   Nom d'utilisateur
     * @param string                    $email              -   Adresse e-mail de l'utilisateur
     * @param string                    $status             -   Statut de l'utilisateur
     * 
     * @return void
     */
    public function __construct($id, $username, $email, $status) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->status = $status;
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

        return new self($userData['id'], $userData['username'], $userData['email'], $userData['status']);
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
        

        $db = new Database();

        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        if (password_verify(hash('sha512', $passwd . $userData['salt']), $userData['passwd'])) {
            return new self($userData['id'], $userData['username'], $userData['email'], $userData['status']);
        } else {
            return new self(null, null, null, null);
        }

        
    }


    /**
     * Getter de id
     * 
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getter du nom d'utilisateur
     * 
     * @return int|null
     */
    public function getUsername() {
        return $this->username;
    }


    /**
     * Getter de l'email de l'utilisateur
     * 
     * @return string|null
     */
    public function getEmail() {
        return $this->email;
    }


    /**
     * Getter du statut de l'utilisateur
     * 
     * @return string|null
     */
    public function getStatus() {
        return $this->status;
    }


}



?>