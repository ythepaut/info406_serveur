<?php

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation configuration
include_once('../../config/core.php');

//Headers API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//Importation automatique des classes
require_once("../../config/Autoloader.php");
Autoloader::register();

//Acquisition des données de la requete POST
$requestData = (!empty($_POST)) ? $_POST : $_GET;

//Traitement

if (!empty($requestData['token']) && !empty($requestData['content']) && !empty($requestData['destination']) && !empty($requestData['id'])) {

    if (is_numeric($requestData['id'])) {

        if ($requestData['destination'] == MessageDestinationType::HUMANRESOURCE || $requestData['destination'] == MessageDestinationType::PROJECT || $requestData['destination'] == MessageDestinationType::HUMANRESOURCE_ALLOCATION || $requestData['destination'] == MessageDestinationType::MATERIALRESOURCE_ALLOCATION) {

            //Verification permissions et de l'existance de la destination
            $authorized = false;
            $exists = false;

            switch ($requestData['destination']) {
            
                case MessageDestinationType::HUMANRESOURCE:
                    $authorized = $authorized || PermissionManager::getInstance($jwtConfig['key'])->isTokenValid($requestData['token']);
                    $exists = HumanResource::createByID(intval($requestData['id']))->getId() !== null;
                    break;
                case MessageDestinationType::PROJECT:
                    $authorized = $authorized || PermissionManager::getInstance($jwtConfig['key'])->canAccessProject($requestData['token'], intval($requestData['id']));
                    $exists = Project::createByID(intval($requestData['id']))->getId() !== null;
                    break;
                case MessageDestinationType::HUMANRESOURCE_ALLOCATION:
                    $authorized = $authorized || false; //TODO Verification autorisation allocation // Faire valider la methode de resolution de conflit.
                    $exists = false;
                    break;
                case MessageDestinationType::MATERIALRESOURCE_ALLOCATION:
                    $authorized = $authorized || false; //TODO Verification autorisation allocation // Faire valider la methode de resolution de conflit.
                    $exists = false;
                    break;
                default:
                    $authorized = false;
                    $exists = false;
                    break;

            }

            if ($authorized && $exists) {

                $user = User::createByID(PermissionManager::getInstance($jwtConfig['key'])->getUserID($requestData['token']));
                $message = new Message(null, $requestData['content'], time(), $user->getIdHResource(), $requestData['destination'], $requestData['id']);

                $message->createMessage();
        
                $response = new Response(ResponseEnum::SUCCESS_MESSAGE_CREATED, array(), ResponseType::JSON);
                $response->addContent(array("message" => array("id" => $message->getId(),
                                                               "content" => $message->getContent(),
                                                               "date" => $message->getDate(),
                                                               "sourceId" => $message->getSourceId(),
                                                               "destination" => $message->getDestinationType(),
                                                               "destinationId" => $message->getDestinationId())));
                $response->sendResponse();

            } elseif (!$authorized) {
                $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
                $response->sendResponse();
            } else {
                $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => (($requestData['destination'] == MessageDestinationType::HUMANRESOURCE) ? "HumanResource" : "Project") . ":" . $requestData['id']), ResponseType::JSON);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("destination")), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {

        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
        $response->addInvalidIntArguments(array("id"), $requestData);
        $response->sendResponse();

    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "content", "destination", "id"), $requestData);
    $response->sendResponse();

}