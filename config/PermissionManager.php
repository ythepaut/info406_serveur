<?php

include_once('core.php');

class PermissionManager {

    private static $instance = null;
    private $key;


    /**
     * Constructeur du gestionnaire de permissions
     * 
     * @param string                    $key                -   Clé secrete JWT
     */
    public function __construct($key) {
        $this->key = $key;
    }


    /**
     * Getteur de l'instance
     * 
     * @param string                    $key                -   Clé secrete JWT
     */
    public static function getInstance($key) : self {

        if (self::$instance === null) {
            self::$instance = new self($key);
        }

        return self::$instance;

    }


    /**
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut créer un projet.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return bool
     */
    public function canCreateProject(string $token) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);
            
            if ($humanResource !== null) {

                if ($humanResource->getRole() != HumanResourceRole::PROJECT_LEADER) {
                    $allowed = false;
                }

            } else {
                $allowed = false;
            }

        } else {
            $allowed = false;
        }

        return $allowed;

    }



    /**
     * Fonction qui retourne vrai si le jeton est valide.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return bool
     */
    private function isTokenValid(string $token) {

        $res = false;

        try {
            $decoded = JWT::decode($token, $this->key, array('HS256'));
            $res = $decoded !== null;
        } catch (Exception $e) {
            $res = false;
        }

        return $res;

    }




}


?>