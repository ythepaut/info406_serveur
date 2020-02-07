<?php

class Response {

    private $response;        //Statut de la requête
    private $content;       //Contenu de la réponse (e.g. token, taskList, ...)
    private $responseType;  //Type de réponse (e.g. JSON, HTML, XML)


    /**
     * Constructeur de la classe Response
     * 
     * @param Enum->ResponseEnum        $response           -   Statut et message de la requête
     * @param array                     $content            -   Contenu de la réponse (e.g. token, taskList, ...)
     * @param Enum->ResponseType        $responseType       -   Type de réponse (e.g. JSON, HTML, XML)
     */
    public function __construct(array $response, array $content, int $responseType) {
        $this->response = $response;
        $this->content = $content;
        $this->responseType = $responseType;
    }


    /**
     * Methode qui affiche la réponse.
     * 
     * @return void
     */
    public function sendResponse() : void {

        switch ($this->response['status']) {

            case ResponseStatus::SUCCESS:
                http_response_code(200);
                break;
            case ResponseStatus::WARNING:
            case ResponseStatus::ERROR:
                http_response_code(400);
                break;
            default:
                http_response_code(200);
                break;
        }

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

        $response = array("status" => $this->response['status'],
                          "message" => $this->response['verbose'],
                          "content" => $this->content);
        
        return json_encode($response, true);

    }


    /**
     * TODO Methode qui convertit la réponse en code XML.
     * 
     * @return string
     */
    private function toXML() : string {

        return "";
    
    }


    /**
     * TODO Methode qui convertit la réponse en code HTML.
     * 
     * @return string
     */
    private function toHTML() : string {

        return "";
    
    }


    /**
     * Methode qui permet d'ajouter du contenu dans la réponse.
     */
    public function addContent(array $content) {
        array_push($this->content, $content);
    }


}



?>