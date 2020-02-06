<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation automatique des classes
require_once("../config/Autoloader.php");
Autoloader::register();


$database = new Database();
$connection = $database->getConnection();

$user = new User($connection);

$requestData = json_decode(file_get_contents("php://input"));


//$user->setUsername($requestData->username);

if ($user->usernameExists($requestData->username)) {

    $testResponse = new Response(ResponseStatus::SUCCESS, "Ceci est un test", array("arg1" => "val1", "arg2" => "val2"), ResponseType::JSON);
    $testResponse->sendResponse();

} else {

    $testResponse = new Response(ResponseStatus::ERROR, "Ceci est un test", array("arg1" => "val1", "arg2" => "val2"), ResponseType::JSON);
    $testResponse->sendResponse();
}