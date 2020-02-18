<?php

class Response {

    private $response;      //Statut de la requête
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

        http_response_code($this->response['http-code']);

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
                          "code" => $this->response['code'],
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


    /** Methode qui ajoute en contenu les arguments manquants de la requête
     * 
     * @param array                     $required           -   Liste des arguments nécessaires à la requête
     * @param array                     $given              -   Liste des arguments fournis
     * 
     * @return void
     */
    public function addMissingArguments(array $required, array $given) : void {

        $missing = array();

        foreach ($required as $arg) {
            if (empty($given[$arg])) {
                array_push($missing, $arg);
            }
        }

        $this->addContent(array("missing" => $missing));

    }


    /** Methode qui ajoute en contenu les arguments qui ne sont pas des entiers
     * 
     * @param array                     $intArgs            -   Liste des arguments supposés être des entiers
     * @param array                     $given              -   Liste des arguments fournis
     * 
     * @return void
     */
    public function addInvalidIntArguments(array $intArgs, array $given) : void {

        $invalid = array();

        foreach ($intArgs as $arg) {
            if (!is_numeric($given[$arg])) {
                array_push($invalid, $arg);
            }
        }

        $this->addContent(array("invalid" => $invalid));

    }


}



?>