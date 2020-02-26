<?php

/**
 * Classe salle
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Room {

    const TABLE_NAME = "g4_room";

    private $number;
    private $type;
    private $seats;
    private $computers;


    /**
     * Constructeur de la classe salle.
     * 
     * @param int                       $number             -   Numéro de salle (identifie la salle)
     * @param Enum->RoomType            $type               -   Type de salle
     * @param int                       $seats              -   Nombre de places
     * @param int                       $computers          -   Nombre d'ordinateurs
     * 
     * @return void
     */
    public function __construct($number, $type, $seats, $computers) {
        $this->number = $number;
        $this->type = $type;
        $this->seats = $seats;
        $this->computers = $computers;
    }


    /**
     * Fabrique de la classe salle à partir de son numéro.
     * 
     * @param int                       $id                 -   Numéro de salle
     * 
     * @return self
     */
    public static function createByID(int $id) : self {
        
        $db = Database::getInstance();

        //Acquisition données de la salle
        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE number = ?");
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $roomData = $result->fetch_assoc();
        $query->close();

        return new self($roomData['number'], $roomData['type'], $roomData['seats'], $roomData['computers']);
    }


    /**
     * Fonction qui retourne vrai si le numéro de salle est déjà dans la base
     * 
     * @param int                       $number             -   Numéro de salle
     * 
     * @return bool
     */
    public static function isNumberUsed(int $number) : bool {
        
        $db = Database::getInstance();

        $query = $db->getConnection()->prepare("SELECT number FROM " . self::TABLE_NAME . " WHERE number = ?");
        $query->bind_param("i", $number);
        $query->execute();

        $result = $query->get_result();
        $query->close();
        $roomData = $result->fetch_assoc();

        return $roomData['number'] !== null;
    }


    /**
     * Fonction qui insere la salle dans la base de donnée
     * 
     * @return void
     * 
     * @throws UniqueDuplicationException                   -   Numéro de salle déjà utilisé
     */
    public function createRoom() : void {

        $db = Database::getInstance();
        
        if (!self::isNumberUsed($this->number)) {

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (number, type, seats, computers) VALUES (?,?,?,?)");
            $query->bind_param("isii", $this->number, $this->type, $this->seats, $this->computers);
            $query->execute();
            $query->close();

            //Recuperation des données insérées
            $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE number = ?");
            $query->bind_param("i", $this->number);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $roomData = $result->fetch_assoc();

            $this->__construct($roomData['number'], $roomData['type'], $roomData['seats'], $roomData['computers']);

        } else {
            throw new UniqueDuplicationException("Room number '" . $this->number . "' already used in database." , 2);
        }
        
    }


    /**
     * Fonction qui retourne la liste des salles
     * 
     * @return array
     */
    public static function getRoomList() : array {

        $db = Database::getInstance();

        $list = array();
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::TABLE_NAME);

        while ($roomData = mysqli_fetch_assoc($query)) {
            array_push($list, self::createByID($roomData['number']));
        }

        return $list;
    }


    /**
     * Getteur du numéro de la salle
     */
    public function getNumber() {
        return $this->number;
    }


    /**
     * Getteur du type de salle
     */
    public function getType() {
        return $this->type;
    }


    /**
     * Getteur du nombre de places
     */
    public function getSeats() {
        return $this->seats;
    }


    /**
     * Getteur du nombre d'ordinateurs dans la salle
     */
    public function getComputers() {
        return $this->computers;
    }


}



?>