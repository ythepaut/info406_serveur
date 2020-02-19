<?php

/**
 * Classe ressource humaine
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class HumanResource {

    const TABLE_NAME = "g4_h_resource";

    private $id;
    private $firstname;
    private $lastname;
    private $job;
    private $role;


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID de la ressource
     * @param string                    $firstname          -   Prenom
     * @param string                    $lastname           -   Nom
     * @param string                    $job                -   Metier/spécialité
     * @param Enum->HumanResourceRole   $role               -   Role (Collaborateur/chef de projet)
     * 
     * @return void
     */
    public function __construct($id, $firstname, $lastname, $job, $role) {
        $this->id = $id;
        $this->firstname = ucfirst(strtolower($firstname));
        $this->lastname = strtoupper($lastname);
        $this->job = $job;
        $this->role = $role;
    }


    /**
     * Fabrique de la classe ressource humaine à partir de l'ID.
     * 
     * @param int                       $id                 -   ID de la ressource
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
        $resourceData = $result->fetch_assoc();

        return new self($resourceData['id'], $resourceData['firstname'], $resourceData['lastname'], $resourceData['job'], $resourceData['role']);
    }



    /**
     * Getteur de l'id de la ressource
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getteur du prenom de la ressource
     */
    public function getFirstName() {
        return $this->firstname;
    }


    /**
     * Getteur du nom de la ressource
     */
    public function getLastName() {
        return $this->lastname;
    }


    /**
     * Getteur du metier de la ressource
     */
    public function getJob() {
        return $this->job;
    }


    /**
     * Getteur du role de la ressource
     */
    public function getRole() {
        return $this->role;
    }


    /**
     * Fonction qui insere la ressource dans la base de données
     * 
     * @param int                       $userid             -   Utilisateur associé à la ressource
     * 
     * @return void
     * 
     * @throws Exception                                    -   Utilisateur est déjà associé à une autre ressource
     */
    public function createResource(int $userid) : void {

        $db = Database::getInstance();
        $user = User::createByID($userid);

        //Verification que l'utilisateur n'a pas déjà de ressource associée.
        $query = $db->getConnection()->prepare("SELECT id_h_resource FROM " . User::TABLE_NAME . " WHERE id = ?");
        $id = $user->getId();
        $query->bind_param("i", $id);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        if ($userData['id_h_resource'] == 0) {

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (firstname, lastname, job, role) VALUES (?,?,?,?)");
            $query->bind_param("ssss", $this->firstname, $this->lastname, $this->job, $this->role);
            $query->execute();
            $query->close();

            $insertId = mysqli_insert_id($db->getConnection());

            //MAJ de la clé etrangère de l'utilisateur
            $query = $db->getConnection()->prepare("UPDATE " . User::TABLE_NAME . " SET id_h_resource = ? WHERE id = ?");
            $id = $user->getId();
            $query->bind_param("ii", $insertId, $id);
            $query->execute();
            $query->close();
    
            //Recuperation des données et creation de l'objet
            $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
            $query->bind_param("i", $insertId);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $resourceData = $result->fetch_assoc();
    
            $this->__construct($resourceData['id'], $resourceData['firstname'], $resourceData['lastname'], $resourceData['job'], $resourceData['role']);

        } else {
            throw new Exception("User '" . $user->getUsername() . "' (" . $user->getId() . ") is already linked with a resource." , 3);
        }
        
    }


}



?>