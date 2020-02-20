<?php

/**
 * Classe message
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
class Message {

    const TABLE_NAME = "g4_message";

    private $id;
    private $content;
    private $date;
    private $sourceId;
    private $destinationType;   //Enum si destination projet/ressource/...
    private $destinationId;     //Id du destinataire


    /**
     * Constructeur de la classe projet.
     * 
     * @param int                       $id                 -   ID du message
     * @param string                    $content            -   Contenu du message
     * @param int                       $date               -   Date d'emission du message
     * @param int                       $sourceId           -   ID de la ressource expeditrice du message
     * @param Enum->MessageDestinationType  $destinationType-   Type de destination (projet/ressource/...)
     * @param int                       $destinationId      -   ID destination
     * 
     * @return void
     */
    public function __construct($id, $content, $date, $sourceId, $destinationType, $destinationId) {
        $this->id = $id;
        $this->content = $content;
        $this->date = $date;
        $this->sourceId = $sourceId;
        $this->destinationType = $destinationType;
        $this->destinationId = $destinationId;
    }


    /**
     * Fabrique de la classe message à partir de l'ID.
     * 
     * @param int                       $id                 -   ID du projet
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
        $messageData = $result->fetch_assoc();

        $destinationType = null;
        $destinationId = null;

        if (!empty($messageData['id_hr_destination'])) {
            $destinationType = MessageDestinationType::HUMANRESOURCE;
            $destinationId = $messageData['id_hr_destination'];
        } elseif (!empty($messageData['id_project_destination'])) {
            $destinationType = MessageDestinationType::PROJECT;
            $destinationId = $messageData['id_project_destination'];
        } elseif (!empty($messageData['id_alloc_h_destination'])) {
            $destinationType = MessageDestinationType::HUMANRESOURCE_ALLOCATION;
            $destinationId = $messageData['id_alloc_h_destination'];
        } elseif (!empty($messageData['id_alloc_m_destination'])) {
            $destinationType = MessageDestinationType::MATERIALRESOURCE_ALLOCATION;
            $destinationId = $messageData['id_alloc_m_destination'];
        }

        return new self($messageData['id'], $messageData['content'], $messageData['date'], $messageData['id_source'], $destinationType, $destinationId);
    }


    /**
     * Fonction qui insere le message dans la base de donnée
     * 
     * @return void
     */
    public function createMessage() : void {

        $idColumn = "";

        switch ($this->destinationType) {
            
            case MessageDestinationType::HUMANRESOURCE:
                $idColumn = "id_hr_destination";
                break;
            case MessageDestinationType::PROJECT:
                $idColumn = "id_project_destination";
                break;
            case MessageDestinationType::HUMANRESOURCE_ALLOCATION:
                $idColumn = "id_alloc_h_destination";
                break;
            case MessageDestinationType::MATERIALRESOURCE_ALLOCATION:
                $idColumn = "id_alloc_m_destination";
                break;
            default:
                break;

        }

        $db = Database::getInstance();

        //Insertion dans la base
        $query = $db->getConnection()->prepare("INSERT INTO " . self::TABLE_NAME . " (content, date, id_source, " . $idColumn . ") VALUES (?,?,?,?)");
        $query->bind_param("siii", $this->content, $this->date, $this->sourceId, $this->destinationId);
        $query->execute();
        $query->close();

        $insertId = mysqli_insert_id($db->getConnection());

        //Recuperation des données (notamment l'ID)
        $query = $db->getConnection()->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = ?");
        $query->bind_param("i", $insertId);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $messageData = $result->fetch_assoc();

        if (!empty($messageData['id_hr_destination'])) {
            $destinationType = MessageDestinationType::HUMANRESOURCE;
            $destinationId = $messageData['id_hr_destination'];
        } elseif (!empty($messageData['id_project_destination'])) {
            $destinationType = MessageDestinationType::PROJECT;
            $destinationId = $messageData['id_project_destination'];
        } elseif (!empty($messageData['id_alloc_h_destination'])) {
            $destinationType = MessageDestinationType::HUMANRESOURCE_ALLOCATION;
            $destinationId = $messageData['id_alloc_h_destination'];
        } elseif (!empty($messageData['id_alloc_m_destination'])) {
            $destinationType = MessageDestinationType::MATERIALRESOURCE_ALLOCATION;
            $destinationId = $messageData['id_alloc_m_destination'];
        }

        $this->__construct($messageData['id'], $messageData['content'], $messageData['date'], $messageData['id_source'], $destinationType, $destinationId);
        
    }


    /**
     * Getteur de l'id du message
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Getteur du contenu du message
     */
    public function getContent() {
        return $this->content;
    }


    /**
     * Getteur de la date du message
     */
    public function getDate() {
        return $this->date;
    }


    /**
     * Getteur de la destination du message
     */
    public function getDestinationType() {
        return $this->destinationType;
    }


    /**
     * Getteur de l'id de destination
     */
    public function getDestinationId() {
        return $this->destinationId;
    }


}



?>