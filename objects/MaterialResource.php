<?php

/**
 * Classe ressource matérielle
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class MaterialResource {

    const TABLE_NAME = "g4_m_resource";

    private $id;
    private $name;
    private $description;


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID de la ressource
     * @param string                    $name               -   Nom de la ressource
     * @param string                    $description        -   Description de la ressource
     * 
     * @return void
     */
    public function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }


    /**
     * Fabrique de la classe ressource materielle à partir de l'ID.
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

        return new self($resourceData['id'], $resourceData['name'], $resourceData['description']);
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
    public function getName() {
        return $this->name;
    }


    /**
     * Getteur de la description de la ressource
     */
    public function getDescription() {
        return $this->description;
    }


    /**
     * Fonction qui insere la ressource dans la base de données
     * 
     * @return void
     */
    public function createResource() : void {

        $db = Database::getInstance();

        //Insertion dans la base
        $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (name, description) VALUES (?,?)");
        $query->bind_param("ss", $this->name, $this->description);
        $query->execute();
        $query->close();

        $insertId = mysqli_insert_id($db->getConnection());

        //Recuperation des données (notamment l'ID)
        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $insertId);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $resourceData = $result->fetch_assoc();

        $this->__construct($resourceData['id'], $resourceData['name'], $resourceData['description']);
        
    }


}



?>