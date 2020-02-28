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

if (!empty($requestData['token']) && !empty($requestData['number'])) {

    if (PermissionManager::getInstance($jwtConfig['key'])->canCreateRoom($requestData['token'])) {//Verification permission
        
        if (is_numeric($requestData['number'])) {

            if (((!empty($requestData['seats']) && is_numeric($requestData['seats'])) || empty($requestData['seats'])) &&
                ((!empty($requestData['computers']) && is_numeric($requestData['computers'])) || empty($requestData['computers']))) {

                if ((!empty($requestData['type']) && ($requestData['type'] == RoomType::ROOM ||
                                                      $requestData['type'] == RoomType::MEETING_ROOM ||
                                                      $requestData['type'] == RoomType::CONFERENCE_ROOM ||
                                                      $requestData['type'] == RoomType::DESK ||
                                                      $requestData['type'] == RoomType::OTHER)) || empty($requestData['type'])) {

                    $seats = (!empty($requestData['seats'])) ? $requestData['seats'] : -1;
                    $computers = (!empty($requestData['computers'])) ? $requestData['computers'] : -1;
                    $type = (!empty($requestData['type'])) ? $requestData['type'] : RoomType::ROOM;

                    $room = new Room(intval($requestData['number']), $type, $seats, $computers);

                    try {

                        $room->createRoom();
        
                        $response = new Response(ResponseEnum::SUCCESS_ROOM_CREATED, array(), ResponseType::JSON);
                        $response->addContent(array("room" => array("number" => $room->getNumber(),
                                                                    "type" => $room->getType(),
                                                                    "seats" => $room->getSeats(),
                                                                    "computers" => $room->getComputers())));
                        $response->sendResponse();

                    } catch (UniqueDuplicationException $e) {

                        $response = new Response(ResponseEnum::ERROR_ROOM_NUMBER_USED, array(), ResponseType::JSON);
                        $response->sendResponse();

                    }

                } else {
                    $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("type")), ResponseType::JSON);
                    $response->sendResponse();
                }

            } else {
                $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
                $response->addInvalidIntArguments(array("seats", "computers"), $requestData);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
            $response->addInvalidIntArguments(array("number"), $requestData);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "number"), $requestData);
    $response->sendResponse();

}