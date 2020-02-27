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

if (!empty($requestData['token'])) {

    if (PermissionManager::getInstance($jwtConfig['key'])->isTokenValid($requestData['token'])) {
        
        if ((!empty($requestData['from']) && is_numeric($requestData['from'])) || empty($requestData['from'])) {
        
            if ((!empty($requestData['to']) && is_numeric($requestData['to'])) || empty($requestData['to'])) {

                if ((!empty($requestData['project']) && is_numeric($requestData['project'])) || empty($requestData['project'])) {

                    if ((!empty($requestData['task']) && is_numeric($requestData['task'])) || empty($requestData['task'])) {

                        if ((!empty($requestData['hresource']) && is_numeric($requestData['hresource'])) || empty($requestData['hresource'])) {
                

                            $from = (!empty($requestData['from'])) ? $requestData['from'] : 0;
                            $to = (!empty($requestData['to'])) ? $requestData['to'] : 9999999999;
                            $project = (!empty($requestData['project'])) ? $requestData['project'] : 0;
                            $task = (!empty($requestData['task'])) ? $requestData['task'] : 0;
                            $hresource = (!empty($requestData['hresource'])) ? $requestData['hresource'] : 0;


                            if ($project == 0 || PermissionManager::getInstance($jwtConfig['key'])->canAccessProject($requestData['token'], $project)) {

                                if ($project == 0 || Project::createByID($project)->getId() !== null) {

                                    if ($task == 0 || PermissionManager::getInstance($jwtConfig['key'])->canAccessTask($requestData['token'], $task)) {
            
                                        if ($task == 0 || Task::createByID($task)->getId() !== null) {

                                            if ($hresource == 0 || HumanResource::createByID($hresource)->getId() !== null) {


                                                $timeslots = Timeslot::getTimeslotList();
                                                
                                                $list = array();
                                                foreach ($timeslots as $timeslot) {

                                                    if ($timeslot->getDateStart() > $from && $timeslot->getDateStart() < $to) { //Test si dans la plage de date demandée

                                                        //Test si le projet correspond au filtre par projet
                                                        $taskInstance = Task::createByID($timeslot->getTask());
                                                        if ($project == 0 || $project == $taskInstance->getProject()) {

                                                            if ($task == 0 || $task == $timeslot->getTask()) { //Si la tâche correspond au filtre par tâche
                                                        
                                                                if ($hresource == 0 || in_array(HumanResource::createById($hresource), $taskInstance->getAssignedHumanResources())) { //Si la ressource correspond au filtre

                                                                    array_push($list, array($timeslot->getId() => array("id" => $timeslot->getId(),
                                                                                                                        "start" => $timeslot->getDateStart(),
                                                                                                                        "end" => $timeslot->getDateEnd(),
                                                                                                                        "task" => $timeslot->getTask(),
                                                                                                                        "room" => $timeslot->getRoom())));
                
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                        
                                                $response = new Response(ResponseEnum::SUCCESS_TIMESLOTS_LISTED, array("timeslots" => $list), ResponseType::JSON);
                                                $response->sendResponse();

                                    
                                            } else {
                                                $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "HumanResource:" . $requestData['hresource']), ResponseType::JSON);
                                                $response->sendResponse();
                                            }
                                    
                                        } else {
                                            $response = new Response(ResponseEnum::ERROR_ENTITY_NOT_FOUND, array("entity" => "Task:" . $requestData['task']), ResponseType::JSON);
                                            $response->sendResponse();
                                        }

                                    } else {
                                        $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
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
                            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("hresource")), ResponseType::JSON);
                            $response->sendResponse();
                        }

                    } else {
                        $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("task")), ResponseType::JSON);
                        $response->sendResponse();
                    }

                } else {
                    $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("project")), ResponseType::JSON);
                    $response->sendResponse();
                }

            } else {
                $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("to")), ResponseType::JSON);
                $response->sendResponse();
            }

        } else {
            $response = new Response(ResponseEnum::ERROR_INVALID_ARGUMENT, array("invalid" => array("from")), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_ACCESS_DENIED, array(), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("token"), $requestData);
    $response->sendResponse();

}