<?php

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
     * @param int                       $id                 -   ID du projet
     * @param string                    $firstname          -   Prenom
     * @param string                    $lastname           -   Nom
     * @param string                    $job                -   Metier/spécialité
     * @param Enum->HumanResourceRole   $role               -   Role (Collaborateur/chef de projet)
     * 
     * @return void
     */
    public function __construct($id, $firstname, $lastname, $job, $role) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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
     * Getteur du role de la ressource
     */
    public function getRole() {
        return $this->role;
    }


}



?>