<?php

/**
 * Classe projet
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Project {

    const TABLE_NAME = "g4_project";
    const H_ALLOC_TABLE_NAME = "g4_h_alloc";
    const M_ALLOC_TABLE_NAME = "g4_m_alloc";

    private $id;
    private $name;
    private $description;
    private $deadline;
    private $status;

    private $humanResources;


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID du projet
     * @param string                    $name               -   Nom du projet
     * @param string                    $description        -   Description du projet
     * @param int                       $deadline           -   Date limite du projet
     * @param Enum->ProjectStatus       $status             -   Statut du projet
     * @param array                     $humanResources     -   Liste des ressources humaines associées
     * 
     * @return void
     */
    public function __construct($id, $name, $description, $deadline, $status, $humanResources) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->deadline = $deadline;
        $this->status = $status;
        $this->humanResources = $humanResources;
    }


    /**
     * Fabrique de la classe projet à partir de l'ID.
     * 
     * @param int                       $id                 -   ID du projet
     * 
     * @return self
     */
    public static function createByID(int $id) : self {
        
        $db = Database::getInstance();

        //Acquisition données du projet
        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $projectData = $result->fetch_assoc();

        //Acquisition des ressources allouées au projet
        $humanResources = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::H_ALLOC_TABLE_NAME . " WHERE id_project = " . $id);

        while ($hrData = mysqli_fetch_assoc($query)) {
            array_push($humanResources, HumanResource::createByID($hrData['id_resource']));
        }
        $query->close();

        return new self($projectData['id'], $projectData['name'], $projectData['description'], $projectData['deadline'], $projectData['status'], $humanResources);
    }


    public static function isNameUsed(string $name) : bool {
        
        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("SELECT id FROM " . self::TABLE_NAME . " WHERE name = ?");
        $query->bind_param("s", $name);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $projectData = $result->fetch_assoc();

        return $projectData['id'] !== null;
    }


    /**
     * Fonction qui insere le projet dans la base de donnée
     * 
     * @return void
     * 
     * @throws UniqueDuplicationException                   -   Nom de projet déjà utilisé
     */
    public function createProject() : void {

        $db = Database::getInstance();

        //Verification duplication du nom
        $query = $db->getConnection()->prepare("SELECT id FROM " . self::TABLE_NAME . " WHERE name = ?");
        $query->bind_param("s", $this->name);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $projectData = $result->fetch_assoc();
        
        if ($projectData['id'] === null) {

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (name, description, deadline, status) VALUES (?,?,?,?)");
            $query->bind_param("ssis", $this->name, $this->description, $this->deadline, $this->status);
            $query->execute();
            $query->close();

            //Recuperation des données (notamment l'ID)
            $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE name = ?");
            $query->bind_param("s", $this->name);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $projectData = $result->fetch_assoc();

            $this->__construct($projectData['id'], $projectData['name'], $projectData['description'], $projectData['deadline'], $projectData['status']);

        } else {
            throw new UniqueDuplicationException("Project name '" . $this->name . "' already used in database." , 2);
        }
        
    }


    /**
     * Fonction qui retourne la liste des projets
     * 
     * @return array
     */
    public static function getProjectList() : array {

        $db = Database::getInstance();

        $list = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::TABLE_NAME);

        while ($projectData = mysqli_fetch_assoc($query)) {
            array_push($list, self::createByID($projectData['id']));
        }

        return $list;
    }


    /**
     * Getteur de l'id du projet
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getteur du nom du projet
     */
    public function getName() {
        return $this->name;
    }


    /**
     * Getteur de la description du projet
     */
    public function getDescription() {
        return $this->description;
    }


    /**
     * Getteur de la date limite du projet
     */
    public function getDeadline() {
        return $this->deadline;
    }


    /**
     * Getteur du statut du projet
     */
    public function getStatus() {
        return $this->status;
    }


    /**
     * Getteur de la liste des ressources humaines associées
     */
    public function getHumanResources() {
        return $this->humanResources;
    }


}



?>