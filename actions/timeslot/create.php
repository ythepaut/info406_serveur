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

if (!empty($requestData['token']) && !empty($requestData['start']) && !empty($requestData['end']) && !empty($requestData['task']) && !empty($requestData['room'])) {

    if (is_numeric($requestData['task']) && is_numeric($requestData['room']) && is_numeric($requestData['start']) && is_numeric($requestData['end'])) {
    
        if (intval($requestData['start']) < intval($requestData['end'])) {

            if (PermissionManager::getInstance($jwtConfig['key'])->canCreateTimeslot($requestData['token'])) { //Verification permission créer un créneau
                
                if (PermissionManager::getInstance($jwtConfig['key'])->canAccessTask($requestData['token'], intval($requestData['task']))) { //Verification permission accès à la tâche
                
                    $timeslot = new Timeslot(null, intval($requestData['start']), intval($requestData['end']), intval($requestData['task']), intval($requestData['room']));

                    try {

                        $timeslot->createTimeslot();
        
                        $response = new Response(ResponseEnum::SUCCESS_TIMESLOT_CREATED, array(), ResponseType::JSON);
                        $response->addContent(array("timeslot" => array("id" => $timeslot->getId(),
                                                                        "start" => $timeslot->getDateStart(),
                                                                        "end" => $timeslot->getDateEnd(),
                                                                        "task" => $timeslot->getTask(),
                                                                        "room" => $timeslot->getRoom())));
                        $response->sendResponse();

                    } catch (IllegalResourceAccessException $e) {
                        $response = new Response(ResponseEnum::ERROR_ROOM_UNAVAILABLE, array(), ResponseType::JSON);
                        $response->sendResponse();
                    }


                } else {
                    $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
                    $response->sendResponse();
                }
        
            } else {
                $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
                $response->sendResponse();
            }
        
        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("start", "end")), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {

        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
        $response->addInvalidIntArguments(array("start", "end", "task", "room"), $requestData);
        $response->sendResponse();

    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "start", "end", "task", "room"), $requestData);
    $response->sendResponse();
    
}