<?php

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation automatique des classes
require_once("./config/Autoloader.php");
Autoloader::register();


$testResponse = new Response(ResponseStatus::SUCCESS, "Ceci est un test", array("arg1" => "val1", "arg2" => "val2"), ResponseType::JSON);
$testResponse->sendResponse();

?>