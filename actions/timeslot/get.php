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

if (!empty($requestData['token']) && !empty($requestData['id'])) {

    if (is_numeric($requestData['id'])) {

        if (PermissionManager::getInstance($jwtConfig['key'])->isTokenValid($requestData['token'])) {
            
                $timeslot = Timeslot::createByID(intval($requestData['id']));
                $taskInstance = ($timeslot->getId() !== null) ? Task::createByID($timeslot->getTask()) : null;

                $canAccessTask = false;
                if ($taskInstance !== null) {
                    $canAccessTask = PermissionManager::getInstance($jwtConfig['key'])->canAccessTask($requestData['token'], $timeslot->getTask());
                }

                if (($taskInstance !== null && in_array(HumanResource::createById((PermissionManager::getInstance($jwtConfig['key'])->getUserID($requestData['token']))), $taskInstance->getAssignedHumanResources())) || $canAccessTask) {

                    $response = new Response(ResponseEnum::SUCCESS_TIMESLOT_ACQUIRED, array("timeslot" => array("id" => $timeslot->getId(),
                                                                                                                "start" => $timeslot->getDateStart(),
                                                                                                                "end" => $timeslot->getDateEnd(),
                                                                                                                "task" => $timeslot->getTask(),
                                                                                                                "room" => $timeslot->getRoom())), ResponseType::JSON);
                    $response->sendResponse();
                        
                } else {
                    $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
                    $response->sendResponse();
                }

        } else {
            $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
            $response->sendResponse();
        }
        
    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
        $response->addInvalidIntArguments(array("id"), $requestData);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token, id"), $requestData);
    $response->sendResponse();

}