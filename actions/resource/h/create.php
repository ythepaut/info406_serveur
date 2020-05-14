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

if (!empty($requestData['token']) && !empty($requestData['firstname']) && !empty($requestData['lastname']) && !empty($requestData['email'])) {

    if (filter_var($requestData['email'], FILTER_VALIDATE_EMAIL)) {

        if (PermissionManager::getInstance($jwtConfig['key'])->canCreateResource($requestData['token'])) {//Verification permission
            
            if (strlen($requestData['firstname']) <= 32) {

                if (strlen($requestData['lastname']) <= 32) {

                    if (strlen($requestData['lastname']) <= 128) {

                        if ((!empty($requestData['role']) && ($requestData['role'] == HumanResourceRole::COLLABORATOR ||
                                                            $requestData['role'] == HumanResourceRole::PROJECT_LEADER ||
                                                            $requestData['role'] == HumanResourceRole::RESOURCE_MANAGER)) || empty($requestData['role'])) {


                            $role = (!empty($requestData['role'])) ? $requestData['role'] : HumanResourceRole::COLLABORATOR;
                            $job = (!empty($requestData['job'])) ? $requestData['job'] : "";

                            try {

                                $humanResource = new HumanResource(null, $requestData['firstname'], $requestData['lastname'], $job, $role, "");

                                $user = new User(null, strtolower($humanResource->getLastName() . substr($humanResource->getFirstName(), 0, 1)), strtolower($requestData['email']), UserStatus::ALIVE, 0);

                                $passwd = randomString(12);
                                $salt = randomString(32);

                                $user->createUser($passwd, $salt);

                                if ($user->getId() !== null) {

                                    $humanResource->createResource($user->getId());

                                    $response = new Response(ResponseEnum::SUCCESS_HUMAN_RESOURCE_CREATED, array(), ResponseType::JSON);
                                    $response->addContent(array("h_resource" => array("id" => $humanResource->getId(),
                                                                                    "firstname" => $humanResource->getFirstName(),
                                                                                    "lastname" => $humanResource->getLastName(),
                                                                                    "job" => $humanResource->getJob(),
                                                                                    "role" => $humanResource->getRole(),
                                                                                    "description" => $humanResource->getDescription()),
                                                                "user" => array("id" => $user->getId(),
                                                                                "username" => $user->getUsername(),
                                                                                "email" => $user->getEmail(),
                                                                                "status" => $user->getStatus(),
                                                                                "id_h_resource" => $humanResource->getId(),
                                                                                "passwd" => $passwd)
                                                                )
                                                          );
                                    $response->sendResponse();

                                } else {

                                    $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "User:?"), ResponseType::JSON);
                                    $response->sendResponse();
                                }


                            } catch (UniqueDuplicationException $e) {

                                $response = new Response(ResponseEnum::ERROR_EMAIL_USED, array(), ResponseType::JSON);
                                $response->sendResponse();

                            } catch (Exception $e) {

                                $response = new Response(ResponseEnum::ERROR_RESOURCE_ALREADY_LINKED, array(), ResponseType::JSON);
                                $response->sendResponse();

                            }


                        } else {
                            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("role")), ResponseType::JSON);
                            $response->sendResponse();
                        }

                    } else {
                        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("job")), ResponseType::JSON);
                        $response->sendResponse();
                    }

                } else {
                    $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("lastname")), ResponseType::JSON);
                    $response->sendResponse();
                }

            } else {
                $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("firstname")), ResponseType::JSON);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("email")), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "firstname", "lastname", "email"), $requestData);
    $response->sendResponse();

}