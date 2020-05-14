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

if (!empty($requestData['token']) && !empty($requestData['origin']) && !empty($requestData['id'])) {

    if (is_numeric($requestData['id'])) {

        $page = (!empty($requestData['page'])) ? $requestData['page'] : 1;
        $page = ($page > 0) ? $page : 1;

        if (is_numeric($page)) {

            if ($requestData['origin'] == MessageDestinationType::HUMANRESOURCE || $requestData['origin'] == MessageDestinationType::PROJECT || $requestData['origin'] == MessageDestinationType::HUMANRESOURCE_ALLOCATION || $requestData['origin'] == MessageDestinationType::MATERIALRESOURCE_ALLOCATION) {


                //Verification permissions et de l'existance de l'origine
                $authorized = false;
                $exists = false;

                switch ($requestData['origin']) {
                
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
    
                    
                    $messages = Message::getMessageList();
        
                    $sortedMessages = array();
                    foreach ($messages as $message) {
                        if (($message->getDestinationType() == MessageDestinationType::PROJECT && $message->getDestinationType() == $requestData['origin'] && $message->getDestinationId() == $requestData['id']) ||
                            ($message->getDestinationType() == MessageDestinationType::HUMANRESOURCE && $message->getDestinationType() == $requestData['origin'] && ($message->getDestinationId() == $requestData['id'] || $message->getSourceId() == $requestData['id']))) {
                            array_push($sortedMessages, array($message->getId() => array("id" => $message->getId(),
                                                                                        "content" => $message->getContent(),
                                                                                        "date" => $message->getDate(),
                                                                                        "sourceId" => $message->getSourceId(),
                                                                                        "destination" => $message->getDestinationType(),
                                                                                        "destinationId" => $message->getDestinationId())));

                        }
                    }


                    //Pagination et harmonisation
                    $sortedMessages = array_slice($sortedMessages, ($page - 1)*50, 50, true);
                    
                    $result = array();
                    if ($page == 1) {
                        $result = $sortedMessages;
                    } else {
                        foreach ($sortedMessages as $message) {
                            array_push($result, $message);
                        }
                    }

                    $response = new Response(ResponseEnum::SUCCESS_MESSAGES_LISTED, array("messages" => $result), ResponseType::JSON);
                    $response->sendResponse();
                    
    
                } elseif (!$authorized) {
                    $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
                    $response->sendResponse();
                } else {
                    $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => (($requestData['origin'] == MessageDestinationType::HUMANRESOURCE) ? "HumanResource" : "Project") . ":" . $requestData['id']), ResponseType::JSON);
                    $response->sendResponse();
                }


            } else {
                $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("origin")), ResponseType::JSON);
                $response->sendResponse();
            }
        
        } else {

            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
            $response->addInvalidIntArguments(array("page"), array("page" => $page));
            $response->sendResponse();
        
        }
        
    } else {

        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
        $response->addInvalidIntArguments(array("id"), $requestData);
        $response->sendResponse();
    
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "origin", "id"), $requestData);
    $response->sendResponse();

}