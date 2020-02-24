<?php

/**
 * Classe créneau
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Timeslot {

    const TABLE_NAME = "g4_timeslot";

    private $id;
    private $dateStart;
    private $dateEnd;
    private $idTask;
    private $idRoom;


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID du créneau
     * @param int                       $dateStart          -   Date unix de début
     * @param int                       $dateEnd            -   Date unix de fin
     * @param int                       $idTask             -   ID de la tâche associée
     * @param int                       $idRoom             -   ID de la salle associée
     * 
     * @return void
     */
    public function __construct($id, $dateStart, $dateEnd, $idTask, $idRoom) {
        $this->id = $id;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->idTask = $idTask;
        $this->idRoom = $idRoom;
    }


    /**
     * Fabrique de la classe créneau à partir de l'ID.
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
        $timeslotData = $result->fetch_assoc();

        return new self($timeslotData['id'], $timeslotData['date_start'], $timeslotData['date_end'], $timeslotData['id_task'], $timeslotData['id_room']);
    }


    /**
     * Fonction qui insere la ressource dans la base de données
     * 
     * @return void
     * 
     * @throws IllegalResourceAccessException               -   Creneau de la salle déjà utilisé
     */
    public function createTimeslot() : void {

        $db = Database::getInstance();

        //Verification que la salle pour le créneau est libre
        $query = mysqli_query($db->getConnection(), "SELECT * FROM " . self::TABLE_NAME . " WHERE id_room = " . $this->idRoom);

        $conflictId = -1; //-1 pour aucun conflit par convention
        while ($timeslotData = mysqli_fetch_assoc($query)) {
            if (max($timeslotData['date_start'], $this->dateStart) <= min($timeslotData['date_end'], $this->dateEnd)) { //Si intersection dans les creneaux
                $conflictId = $timeslotData['id'];
            }
        }

        if ($conflictId == -1) {

            //Insertion dans la base
            $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (date_start, date_end, id_task, id_room) VALUES (?,?,?,?)");
            $query->bind_param("iiii", $this->dateStart, $this->dateEnd, $this->idTask, $this->idRoom);
            $query->execute();
            $query->close();

            $insertId = mysqli_insert_id($db->getConnection());

            //Recuperation des données et creation de l'objet
            $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
            $query->bind_param("i", $insertId);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $timeslotData = $result->fetch_assoc();

            $this->__construct($insertId, $timeslotData['date_start'], $timeslotData['date_end'], $timeslotData['id_task'], $timeslotData['id_room']);

        } else {
            throw new IllegalResourceAccessException("Timeslot conflict with Timeslot:" . $conflictId , 4);
        }
    }



    /**
     * Getteur de l'id du créneau
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getteur du de la date de debut du créneau
     */
    public function getDateStart() {
        return $this->dateStart;
    }


    /**
     * Getteur du de la date de fin du créneau
     */
    public function getDateEnd() {
        return $this->dateEnd;
    }


    /**
     * Getteur de la tâche associée
     */
    public function getTask() {
        return $this->idTask;
    }


    /**
     * Getteur de la salle associée
     */
    public function getRoom() {
        return $this->idRoom;
    }


}



?>