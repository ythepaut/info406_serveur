<?php

/**
 * Classe tâche
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Task {

    const TABLE_NAME = "g4_task";
    const H_ASSIGN_TABLE_NAME = "g4_h_task_assignment";
    const M_ASSIGN_TABLE_NAME = "g4_m_task_assignment";

    private $id;
    private $name;
    private $description;
    private $status;
    private $deadline;
    private $idProject;

    private $assignedHumanResources;


    /**
     * Constructeur de la classe tâche.
     * 
     * @param int                       $id                 -   ID de la tâche
     * @param string                    $name               -   Nom de la tâche
     * @param string                    $description        -   Description de la tâche
     * @param Enum->TaskStatus          $status             -   Statut de la tâche
     * @param int                       $deadline           -   Date limite de la tâche
     * @param int                       $idProject          -   ID du projet associé
     * @param array                     $assignedHumanResources Liste des ressources humaines affectées à la tâche
     * 
     * @return void
     */
    public function __construct($id, $name, $description, $status, $deadline, $idProject, $assignedHumanResources) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->deadline = $deadline;
        $this->idProject = $idProject;
        $this->assignedHumanResources = $assignedHumanResources;
    }


    /**
     * Fabrique de la classe tâche à partir de l'ID.
     * 
     * @param int                       $id                 -   ID de la tâche
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
        $taskData = $result->fetch_assoc();

        //Acquisition des ressources allouées au projet
        $assignedHumanResources = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::H_ASSIGN_TABLE_NAME . " WHERE id_task = " . $id);

        while ($hrData = mysqli_fetch_assoc($query)) {
            array_push($assignedHumanResources, HumanResource::createByID($hrData['id_resource']));
        }
        $query->close();

        return new self($taskData['id'], $taskData['name'], $taskData['description'], $taskData['status'], $taskData['deadline'], $taskData['id_project'], $assignedHumanResources);
    }


    /**
     * Fonction qui insere la tâche dans la base de donnée
     * 
     * @return void
     * 
     * @throws TupleNotFoundException                       -   Projet inexistant
     */
    public function createTask() : void {

        $db = Database::getInstance();
        
        //Verification que le projet existe
        $project = Project::createByID($this->idProject);

        if ($project->getId() !== null) {

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (name, description, status, deadline, id_project) VALUES (?,?,?,?,?)");
            $query->bind_param("sssii", $this->name, $this->description, $this->status, $this->deadline, $this->idProject);
            $query->execute();
            $query->close();
    
            $insertId = mysqli_insert_id($db->getConnection());
    
            $this->__construct($insertId, $this->name, $this->description, $this->status, $this->deadline, $this->idProject);

        } else {
            throw new TupleNotFoundException("Project id '" . $this->idProject . "' does not exist." , 3);
        }

    }


    /**
     * Fonction qui retourne la liste des tâches
     * 
     * @return array
     */
    public static function getTaskList() : array {

        $db = Database::getInstance();

        $list = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::TABLE_NAME);

        while ($taskData = mysqli_fetch_assoc($query)) {
            array_push($list, self::createByID($taskData['id']));
        }

        return $list;
    }


    /**
     * Getteur de l'id de la tâche
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getteur du nom de la tâche
     */
    public function getName() {
        return $this->name;
    }


    /**
     * Getteur de la description de la tâche
     */
    public function getDescription() {
        return $this->description;
    }


    /**
     * Getteur du statut de la tâche
     */
    public function getStatus() {
        return $this->status;
    }


    /**
     * Getteur de la date limite de la tâche
     */
    public function getDeadline() {
        return $this->deadline;
    }


    /**
     * Getteur du projet associé à la tâche
     */
    public function getProject() {
        return $this->idProject;
    }


    /**
     * Getteur de la liste des ressources humaines assignées
     */
    public function getAssignedHumanResources() {
        return $this->assignedHumanResources;
    }


}



?>