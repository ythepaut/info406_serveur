<?php

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation configuration
include_once('../../../config/core.php');
//Importation fonctions utiles
include_once('../../../shared/utils.php');

//Headers API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//Importation automatique des classes
require_once("../../../config/Autoloader.php");
Autoloader::register();

//Acquisition des données de la requete POST
$requestData = (!empty($_POST)) ? $_POST : $_GET;

//Traitement

if (!empty($requestData['token']) && !empty($requestData['name'])) {

    if (PermissionManager::getInstance($jwtConfig['key'])->canCreateResource($requestData['token'])) {//Verification permission
        
        if (strlen($requestData['name']) <= 128) {


            $materialResource = new MaterialResource(null, $requestData['name'], ((empty($requestData['description'])) ? "" : $requestData['description']));
            $materialResource->createResource();

            $response = new Response(ResponseEnum::SUCCESS_MATERIAL_RESOURCE_CREATED, array(), ResponseType::JSON);
            $response->addContent(array("m_resource" => array("id" => $materialResource->getId(),
                                                              "name" => $materialResource->getName(),
                                                              "description" => $materialResource->getDescription())));

            $response->sendResponse();


        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("name")), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "name"), $requestData);
    $response->sendResponse();

}