<?php

class Response {

    private $status;        //Statut de la requête
    private $message;       //Verbose
    private $content;       //Contenu de la réponse (e.g. token, taskList, ...)
    private $responseType;  //Type de réponse (e.g. JSON, HTML, XML)


    /**
     * Constructeur de la classe Response
     * 
     * @param Enum->ResponseStatus      $status             -   Statut de la requête
     * @param string                    $message            -   Message verbeux pour debug
     * @param array                     $content            -   Contenu de la réponse (e.g. token, taskList, ...)
     * @param Enum->ResponseType        $responseType       -   Type de réponse (e.g. JSON, HTML, XML)
     */
    public function __construct($status, $message, $content, $responseType) {
        $this->status = $status;
        $this->message = $message;
        $this->content = $content;
        $this->responseType = $responseType;
    }


    /**
     * Methode qui affiche la réponse.
     * 
     * @return void
     */
    public function sendResponse() : void {

        switch ($this->responseType) {

            case ResponseType::JSON:
                echo($this->toJSON());
                break;
            case ResponseType::XML:
                echo($this->toXML());
                break;
            case ResponseType::HTML:
                echo($this->toHTML());
                break;
            default:
                break;
        }

    }


    /**
     * Methode qui convertit la réponse en code JSON.
     * 
     * @return string
     */
    private function toJSON() : string {

        $response = array("status" => $this->status,
                          "message" => $this->message,
                          "content" => $this->content);
        
        return json_encode($response, true);

    }


    /**
     * Methode qui convertit la réponse en code XML.
     * 
     * @return string
     */
    private function toXML() : string {

        return "";
    
    }


    /**
     * Methode qui convertit la réponse en code HTML.
     * 
     * @return string
     */
    private function toHTML() : string {

        return "";
    
    }


}



?>