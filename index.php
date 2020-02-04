<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("./config/Autoloader.php");
Autoloader::register();

$db = new Database();

$testUser = new User($db->getConnection());
$testUser->setUsername("bob");

echo($testUser->getUsername());


$testResponse = new Response(ResponseStatus::SUCCESS, "Ceci est un test", array("arg1" => "val1", "arg2" => "val2"), ResponseType::JSON);

$testResponse->sendResponse();

?>