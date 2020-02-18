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

if (!empty($requestData['token']) && !empty($requestData['name']) && !empty($requestData['project'])) {

    if (is_int(intval($requestData['project']))) {

        if (PermissionManager::getInstance($jwtConfig['key'])->canCreateTask($requestData['token'], intval($requestData['project']))) {
            
            if (strlen($requestData['name']) >= 3 && strlen($requestData['name']) <= 128) {

                if ((!empty($requestData['deadline']) && is_numeric($requestData['deadline'])) || empty($requestData['deadline'])) {

                    if ((!empty($requestData['status']) && ($requestData['status'] == TaskStatus::PENDING ||
                                                            $requestData['status'] == TaskStatus::ONGOING ||
                                                            $requestData['status'] == TaskStatus::REVIEWING ||
                                                            $requestData['status'] == TaskStatus::FINISHED ||
                                                            $requestData['status'] == TaskStatus::CANCELED)) || empty($requestData['status'])) {

                        $description = (!empty($requestData['description'])) ? $requestData['description'] : "";
                        $deadline = (!empty($requestData['deadline'])) ? $requestData['deadline'] : 0;
                        $status = (!empty($requestData['status'])) ? $requestData['status'] : TaskStatus::PENDING;

                        try {

                            $task = new Task(null, $requestData['name'], $description, $status, $deadline, intval($requestData['project']));
                            $task->createTask();
            
                            $response = new Response(ResponseEnum::SUCCESS_TASK_CREATED, array(), ResponseType::JSON);
                            $response->addContent(array("task" => array("id" => $task->getId(),
                                                                         "name" => $task->getName(),
                                                                         "description" => $task->getDescription(),
                                                                         "status" => $task->getStatus(),
                                                                         "deadline" => $task->getDeadline(),
                                                                         "project" => $task->getProject())));
                            $response->sendResponse();

                        } catch (TupleNotFoundException $ex) {

                                $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "Project:" . $requestData['project']), ResponseType::JSON);
                                $response->sendResponse();

                        }

                    } else {
                        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("status")), ResponseType::JSON);
                        $response->sendResponse();
                    }

                } else {
                    $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("deadline")), ResponseType::JSON);
                    $response->sendResponse();
                }

            } else {
                $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("name")), ResponseType::JSON);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("project")), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token", "name", "project"), $requestData);
    $response->sendResponse();

}