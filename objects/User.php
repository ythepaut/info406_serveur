<?php

/**
 * Classe utilisateur
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class User {

    const TABLE_NAME = "g4_user";

    private $id;
    private $username;
    private $email;
    private $status;
    private $idHResource;


    /**
     * Constructeur de la classe utilisateur.
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * @param string                    $username           -   Nom d'utilisateur
     * @param string                    $email              -   Adresse e-mail de l'utilisateur
     * @param Enum->UserStatus          $status             -   Statut de l'utilisateur
     * @param int                       $idHResource        -   ID de la ressource associée à l'utilisateur
     * 
     * @return void
     */
    public function __construct($id, $username, $email, $status, $idHResource) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->status = $status;
        $this->idHResource = $idHResource;
    }



    /**
     * Fabrique de la classe utilisateur à partir de l'ID.
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * 
     * @return self
     */
    public static function createByID(int $id) : self {
        
        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        return new self($userData['id'], $userData['username'], $userData['email'], $userData['status'], $userData['id_h_resource']);
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
        

        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        if (password_verify(hash('sha512', $passwd . $userData['salt']), $userData['passwd'])) {
            return new self($userData['id'], $userData['username'], $userData['email'], $userData['status'], $userData['id_h_resource']);
        } else {
            return new self(null, null, null, null, null);
        }

        
    }


    public function createUser($passwd, $salt) : void {

        $db = Database::getInstance();

        //Verification duplication de l'email
        $query = $db->getConnection()->prepare("SELECT id FROM " . self::TABLE_NAME . " WHERE email = ?");
        $query->bind_param("s", $this->email);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        if ($userData['id'] === null) {

            $passwdHashed = password_hash(hash('sha512', $passwd . $salt), PASSWORD_DEFAULT, ['cost' => 12]);;

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (username, email, passwd, salt) VALUES (?,?,?,?)");
            $query->bind_param("ssss", $this->username, $this->email, $passwdHashed, $salt);
            $query->execute();
            $query->close();

            //Recuperation des données (notamment l'ID)
            $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE username = ?");
            $query->bind_param("s", $this->username);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $userData = $result->fetch_assoc();

            $this->__construct($userData['id'], $userData['username'], $userData['email'], $userData['status'], $userData['id_h_resource']);

        } else {
            throw new UniqueDuplicationException("User email '" . $this->email . "' already used in database." , 2);
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


    /**
     * Getter de l'id de la ressource associée à l'utilisateur
     * 
     * @return int|null
     */
    public function getIdHResource() {
        return $this->idHResource;
    }


    /**
     * Methode qui recupere le sel du jeton de renouvellement
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * 
     * @return string|null
     */
    public static function getTokenSalt($id) {

        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("SELECT id,token_salt FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        return $userData['token_salt'];
    }


    /**
     * Methode qui modifie le sel du jeton de renouvellement
     * 
     * @param int                       $id                 -   ID de l'utilisateur
     * @param string                    $salt               -   Nouveau sel de jeton
     * 
     * @return void
     */
    public static function setTokenSalt($id, $salt) : void {

        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("UPDATE " . self::TABLE_NAME . " SET token_salt = ? WHERE id = ?");
        $query->bind_param("si", $salt, $id);
        $query->execute();
        $query->close();
    }


}



?>