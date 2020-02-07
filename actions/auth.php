<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation automatique des classes
require_once("../config/Autoloader.php");
Autoloader::register();

//Acquisition des données de la requete POST
//$requestData = json_decode(file_get_contents("php://input"));
$requestData = $_GET;


//Traitement

if (!empty($requestData['username'])) {

    if (!empty($requestData['passwd'])) {


        $user = User::createByCredentials($requestData['username'], $requestData['passwd']);
        
        if ($user->getId() !== NULL) {

            $response = new Response(ResponseEnum::DEBUG_RESPONSE_SUCCESS, array(), ResponseType::JSON);
            $response->sendResponse();

        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_USER_CREDENTIALS, array(), ResponseType::JSON);
            $response->sendResponse();
        }


    } else {
        $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array("argument" => "passwd"), ResponseType::JSON);
        $response->sendResponse();
    }

} else {
    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array("arguments" => "username"), ResponseType::JSON);
    $response->sendResponse();
}