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


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID de la ressource
     * @param string                    $name               -   Nom de la ressource
     * 
     * @return void
     */
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
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

        return new self($resourceData['id'], $resourceData['name']);
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
     * Fonction qui insere la ressource dans la base de données
     * 
     * @return void
     */
    public function createResource() : void {

        $db = Database::getInstance();

        //Insertion dans la base
        $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (name) VALUES (?)");
        $query->bind_param("s", $this->name);
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

        $this->__construct($resourceData['id'], $resourceData['name']);
        
    }


}



?>