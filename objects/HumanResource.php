<?php

/**
 * Classe ressource humaine
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class HumanResource extends Ressource{

    const TABLE_NAME = "g4_h_resource";

    private $id;
    private $firstname;
    private $lastname;
    private $job;
    private $role;
    private $description;


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID de la ressource
     * @param string                    $firstname          -   Prenom
     * @param string                    $lastname           -   Nom
     * @param string                    $job                -   Metier/spécialité
     * @param Enum->HumanResourceRole   $role               -   Role (Collaborateur/chef de projet)
     * @param string                    $description        -   Description de la ressource
     * 
     * @return void
     */
    public function __construct($id, $firstname, $lastname, $job, $role, $description) {
        $this->id = $id;
        $this->firstname = ucfirst(strtolower($firstname));
        $this->lastname = strtoupper($lastname);
        $this->job = $job;
        $this->role = $role;
        $this->description = $description;
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
        
        return new self($resourceData['id'], $resourceData['firstname'], $resourceData['lastname'], $resourceData['job'], $resourceData['role'], $resourceData['description']);
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
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (firstname, lastname, job, role, description) VALUES (?,?,?,?,?)");
            $query->bind_param("sssss", $this->firstname, $this->lastname, $this->job, $this->role, $this->description);
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
    
            $this->__construct($resourceData['id'], $resourceData['firstname'], $resourceData['lastname'], $resourceData['job'], $resourceData['role'], $resourceData['description']);

        } else {
            throw new Exception("User '" . $user->getUsername() . "' (" . $user->getId() . ") is already linked with a resource." , 3);
        }
        
    }


    /**
     * Fonction qui retourne la liste des ressources humaines
     * 
     * @return array
     */
    public static function getRessourceList() : array {

        $db = Database::getInstance();

        $list = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::TABLE_NAME);

        while ($resourceData = mysqli_fetch_assoc($query)) {
            array_push($list, self::createByID($resourceData['id']));
        }

        return $list;
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
     * Getteur de la description de la ressource
     */
    public function getDescription() {
        return $this->description;
    }

}



?>