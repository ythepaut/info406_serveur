<?php

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation configuration
include_once('../config/core.php');

//Headers API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//Importation automatique des classes
require_once("../config/Autoloader.php");
Autoloader::register();

//Acquisition des données de la requete POST
$requestData = (!empty($_POST)) ? $_POST : $_GET;


//Traitement

if (!empty($requestData['token'])) {


    try {

        $decoded = JWT::decode($requestData['token'], $jwtConfig['key'], array('HS256'));
 
        $response = new Response(ResponseEnum::SUCCESS_VALID_TOKEN, array("data" => $decoded->data), ResponseType::JSON);
        $response->sendResponse();
 
    } catch (Exception $e) {

        $response = new Response(ResponseEnum::ERROR_INVALID_TOKEN, array(), ResponseType::JSON);
        $response->sendResponse();

    }


} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token"), $requestData);
    $response->sendResponse();
    
}