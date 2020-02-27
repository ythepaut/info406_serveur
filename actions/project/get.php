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

        if (PermissionManager::getInstance($jwtConfig['key'])->canAccessProject($requestData['token'], intval($requestData['id']))) {
            
            $project = Project::createByID(intval($requestData['id']));

            if ($project->getId() !== null) {

                $response = new Response(ResponseEnum::SUCCESS_PROJECT_ACQUIRED, array("project" => array("id" => $project->getId(),
                                                                                                          "name" => $project->getName(),
                                                                                                          "description" => $project->getDescription(),
                                                                                                          "deadline" => $project->getDeadline(),
                                                                                                          "status" => $project->getStatus())), ResponseType::JSON);
                $response->sendResponse();   

            } else {
                $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "Project:" . $requestData['id']), ResponseType::JSON);
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