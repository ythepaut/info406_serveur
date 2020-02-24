<?php

//DEBUG : Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importation configuration
include_once('../../config/core.php');
include_once('../../shared/utils.php');

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

if (!empty($requestData['username']) && !empty($requestData['passwd'])) {


    $user = User::createByCredentials($requestData['username'], $requestData['passwd']);
    
    if ($user->getId() !== null) {

        //Utilisateur authentifié, verification du statut...
        if ($user->getStatus() == UserStatus::ALIVE) {


            /**
             * JWT de requête : Doit être passé en param. pour les requêtes qui nécessitent une authentification.
             * Contient les informations du l'utilisateur qui s'est connecté.
             * Faible durée, non répudiable.
             */
            $reqTokenData = array("iss"    =>  $jwtConfig['iss'],
                                  "iat"    =>  $jwtConfig['iat'],
                                  "nbf"    =>  $jwtConfig['nbf'],
                                  "exp"    =>  $jwtConfig['req-exp'],
                                  "data"   =>  array("control" =>  array("iat"     =>  $jwtConfig['iat'],
                                                                         "ip"      =>  $_SERVER['REMOTE_ADDR'],
                                                                         "type"         =>  "requests"
                                                                        ),
                                                     "user"    =>  array("id"      =>  $user->getId(),
                                                                         "username"=>  $user->getUsername(),
                                                                         "email"   =>  $user->getEmail(),
                                                                         "status"  =>  $user->getStatus(),
                                                                         "id_h_resource" => $user->getIdHResource()
                                                                        )
                                                    )
            );

            $reqToken = JWT::encode($reqTokenData, $jwtConfig['key']);


            User::setTokenSalt($user->getId(), randomString(32));
            /**
             * JWT de renouvellement : Doit être passé en param. créer un nouveau jeton de requête sans avoir à passer les identifiants utilisateur.
             * Contient les informations nécessaires à l'identification de l'utilisateur pour créer un nouveau jeton de requête.
             * Longue durée, répudiable.
             */
            $renTokenData = array("iss"    =>  $jwtConfig['iss'],
                                  "iat"    =>  $jwtConfig['iat'],
                                  "nbf"    =>  $jwtConfig['nbf'],
                                  "exp"    =>  $jwtConfig['ren-exp'],
                                  "data"   =>  array("control" =>  array("iat"          =>  $jwtConfig['iat'],
                                                                         "ip"           =>  $_SERVER['REMOTE_ADDR'],
                                                                         "type"         =>  "renew"
                                                                        ),
                                                     "user"    =>  array("id"           =>  $user->getId(),
                                                                         "tokensalt"   =>  User::getTokenSalt($user->getId())
                                                                        )
                                                    )
            );
            $renToken = JWT::encode($renTokenData, $jwtConfig['key']);


            //Envoi de la réponse
            $tokens = array("requests-token" => array("value" => $reqToken, "expire" => $jwtConfig['req-exp']),
                            "renew-token" => array("value" => $renToken, "expire" => $jwtConfig['ren-exp']));

            $response = new Response(ResponseEnum::SUCCESS_AUTHENTICATED, $tokens, ResponseType::JSON);
            $response->sendResponse();


        } else {
            $response = new Response(ResponseEnum::WARNING_USER_SUSPENDED, array(), ResponseType::JSON);
            $response->sendResponse();
        }

    } else {
        $response = new Response(ResponseEnum::ERROR_INVALID_USER_CREDENTIALS, array(), ResponseType::JSON);
        $response->sendResponse();
    }

} else {

    $response = new Response(ResponseEnum::ERROR_MISSING_ARGUMENT, array(), ResponseType::JSON);
    $response->addMissingArguments(array("username", "passwd"), $requestData);
    $response->sendResponse();

}