<?php

include_once('core.php');

/**
 * Classe (singleton) qui gère les permissions
 * 
 * @author      Yohann THEPAUT (ythepaut) <contact@ythepaut.com>
 * @copyright   CC BY-NC-SA 4.0
 */
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

                if ($humanResource->getRole() != HumanResourceRole::PROJECT_LEADER && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut acceder au projet
     * 
     * @param string                    $token              -   JWT
     * @param string                    $projectId          -   ID du projet
     * 
     * @return bool
     */
    public function canAccessProject(string $token, int $projectId) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);
            
            if ($humanResource !== null) {

                $project = Project::createByID($projectId);

                if (!in_array($humanResource, $project->getHumanResources()) && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut créer une ressource.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return bool
     */
    public function canCreateResource(string $token) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);
            
            if ($humanResource !== null) {

                if ($humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut créer une tâche au projet.
     * 
     * @param string                    $token              -   JWT
     * @param string                    $projectId          -   ID du projet rattaché à la tâche
     * 
     * @return bool
     */
    public function canCreateTask(string $token, int $projectId) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);

            if ($humanResource !== null) {

                if ($humanResource->getRole() != HumanResourceRole::PROJECT_LEADER && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
                    $allowed = false;
                } else {
                    if (!$this->canAccessProject($token, $projectId)) {
                        $allowed = false;
                    }
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut acceder à la tâche
     * 
     * @param string                    $token              -   JWT
     * @param string                    $taskId             -   ID de la tâche
     * 
     * @return bool
     */
    public function canAccessTask(string $token, int $taskId) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);

            if ($humanResource !== null) {

                $task = Task::createByID($taskId);

                if (!in_array($humanResource, $task->getAssignedHumanResources()) && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut créer un creneau.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return bool
     */
    public function canCreateTimeslot(string $token) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);
            
            if ($humanResource !== null) {

                if ($humanResource->getRole() != HumanResourceRole::PROJECT_LEADER && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut modifier la tâche
     * 
     * @param string                    $token              -   JWT
     * @param string                    $taskId             -   ID de la tâche
     * 
     * @return bool
     */
    public function canEditTimeslot(string $token, int $taskId) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);

            if ($humanResource !== null) {

                $task = Task::createByID($taskId);

                if (!in_array($humanResource, $task->getAssignedHumanResources()) && $humanResource->getRole() != HumanResourceRole::PROJECT_LEADER && $humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si l'utilisateur associé au jeton peut créer une salle.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return bool
     */
    public function canCreateRoom(string $token) : bool {

        $allowed = true;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));
            $humanResource = HumanResource::createByID($user->data->user->id_h_resource);

            if ($humanResource !== null) {

                if ($humanResource->getRole() != HumanResourceRole::RESOURCE_MANAGER) {
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
     * Fonction qui retourne vrai si le jeton de requêtes est valide.
     * 
     * @param string                    $token              -   JWT de requêtes
     * 
     * @return bool
     */
    public function isTokenValid(string $token) {

        $res = false;

        try {
            $decoded = JWT::decode($token, $this->key, array('HS256'));
            $res = $decoded !== null;

            if ($decoded->data->control->type != "requests") {
                $res = false;
            }
            if ($decoded->data->control->ip != $_SERVER['REMOTE_ADDR']) {
                $res = false;
            }

        } catch (Exception $e) {
            $res = false;
        }

        return $res;

    }


    /**
     * Fonction qui retourne vrai si le jeton de renouvellement est valide.
     * 
     * @param string                    $token              -   JWT de renouvellement
     * 
     * @return bool
     */
    public function isRenewTokenValid(string $token) {

        $res = false;

        try {
            $decoded = JWT::decode($token, $this->key, array('HS256'));
            $res = $decoded !== null;

            if ($decoded->data->control->type != "renew") {
                $res = false;
            }
            if ($decoded->data->control->ip != $_SERVER['REMOTE_ADDR']) {
                $res = false;
            }
            if (empty($decoded->data->user->tokensalt) || User::getTokenSalt($decoded->data->user->id) != $decoded->data->user->tokensalt) {
                $res = false;
            }

        } catch (Exception $e) {
            $res = false;
        }

        return $res;

    }


    /**
     * Fonction qui retourne l'id de l'utilisateur du jeton.
     * 
     * @param string                    $token              -   JWT
     * 
     * @return int|null
     */
    public function getUserID(string $token) {

        $userId = null;

        if (self::isTokenValid($token)) {

            $user = JWT::decode($token, $this->key, array('HS256'));

            $userId = $user->data->user->id_h_resource;

        }

        return $userId;

    }




}


?>