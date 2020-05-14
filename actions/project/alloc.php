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

if (!empty($requestData['token']) && !empty($requestData['project']) && !empty($requestData['type']) && !empty($requestData['id']) && !empty($requestData['end'])) {

    if ($requestData['type'] == AllocationType::HUMAN || $requestData['type'] == AllocationType::MATERIAL) {

        if (is_numeric($requestData['project']) && is_numeric($requestData['id']) && is_numeric($requestData['end']) && (empty($requestData['start']) || (!empty($requestData['start']) && is_numeric($requestData['start'])))) {

            $start = (empty($requestData['start'])) ? time() : $requestData['start'];

            if (PermissionManager::getInstance($jwtConfig['key'])->canAddResource($requestData['token'], intval($requestData['project']))) {
                
                $project = Project::createByID(intval($requestData['project']));

                if ($project->getId() !== null) {

                    $ressource = null;

                    if ($requestData['type'] == AllocationType::HUMAN) {
                        $ressource = HumanResource::createByID($requestData['id']);
                    } elseif ($requestData['type'] == AllocationType::MATERIAL) {
                        $ressource = MaterialResource::createByID($requestData['id']);
                    }

                    if ($ressource->getId() !== null) {

                        try {

                            $project->assginToProject($ressource->getId(), $start, $requestData['end'], JWT::decode($requestData['token'], $jwtConfig['key'], array('HS256'))->data->user->id_h_resource);

                            $response = new Response(ResponseEnum::SUCCESS_RESOURCE_ALLOCATED, array(), ResponseType::JSON);
                            $response->sendResponse();
                        

                        } catch (IllegalResourceAccessException $e) {
                            $response = new Response(ResponseEnum::ERROR_ILLEGAL_ENTITY_ACCESS, array(), ResponseType::JSON);
                            $response->sendResponse();
                        }

                    } else {
                        $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => ($requestData['type'] == AllocationType::HUMAN ? "HumanResource" : "MaterialResource") . ":" . $requestData['id']), ResponseType::JSON);
                        $response->sendResponse();
                    }

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
            $response->addInvalidIntArguments(array("project", "id", "end"), $requestData);
            $response->sendResponse();
        }
    
    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => "type"), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "project", "type", "id", "end"), $requestData);
    $response->sendResponse();

}