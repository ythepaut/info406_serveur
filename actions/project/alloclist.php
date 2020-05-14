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

if (!empty($requestData['token']) && !empty($requestData['project'])) {

    if (is_numeric($requestData['project']) && (empty($requestData['date']) || (!empty($requestData['date']) && is_numeric($requestData['date'])))) {

        $date = (empty($requestData['date'])) ? -1 : $requestData['date'];

        if (PermissionManager::getInstance($jwtConfig['key'])->canAccessProject($requestData['token'], intval($requestData['project']))) {
            
            $project = Project::createByID(intval($requestData['project']));

            if ($project->getId() !== null) {

                $humanAllocs = array();
                foreach ($project->getAllocationList("HUMAN") as $alloc) {
                    if ($alloc["id_project"] == $requestData['project'] && ($date == -1 || ($alloc["date_start"] <= $date && $alloc["date_end"] >= $date))) {
                        array_push($humanAllocs, $alloc);
                    }
                }

                $materialAllocs = array();
                foreach ($project->getAllocationList("MATERIAL") as $alloc) {
                    if ($alloc["id_project"] == $requestData['project'] && ($date == -1 || ($alloc["date_start"] <= $date && $alloc["date_end"] >= $date))) {
                        array_push($materialAllocs, $alloc);
                    }
                }
                

                $result = array(
                    "HUMAN" => $humanAllocs,
                    "MATERIAL" => $materialAllocs
                );

                $response = new Response(ResponseEnum::SUCCESS_ALLOCATIONS_LISTED, $result, ResponseType::JSON);
                $response->sendResponse();


            } else {
                $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "Project:" . $requestData['project']), ResponseType::JSON);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
            $response->sendResponse();
        }
        
    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array(), ResponseType::JSON);
        $response->addInvalidIntArguments(array("project"), $requestData);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "project"), $requestData);
    $response->sendResponse();

}