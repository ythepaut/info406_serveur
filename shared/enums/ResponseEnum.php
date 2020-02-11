<?php

abstract class ResponseEnum {

    //--
    //GENERIQUE / COMMUN
    //--

    //DEBUG
    public const DEBUG_RESPONSE_SUCCESS = array("status" => ResponseStatus::SUCCESS, "verbose" => "Debug : Success.", "code" => 200);
    public const DEBUG_RESPONSE_WARNING = array("status" => ResponseStatus::WARNING, "verbose" => "Debug : Warning.", "code" => 400);
    public const DEBUG_RESPONSE_ERROR = array("status" => ResponseStatus::ERROR, "verbose" => "Debug : Error.", "code" => 400);

    //SUCCES

    //ERREURS
    public const ERROR_MISSING_ARGUMENT = array("status" => ResponseStatus::ERROR, "verbose" => "Missing required argument(s).", "code" => 400);
    public const ERROR_INVALID_ARGUMENT = array("status" => ResponseStatus::ERROR, "verbose" => "Invalid stated argument(s)", "code" => 400);


    //--
    //AUTH
    //--

    //SUCCES
    public const SUCCESS_AUTHENTICATED = array("status" => ResponseStatus::SUCCESS, "verbose" => "Authentication successful and JWT generated.", "code" => 200);

    //AVERTISSEMENTS
    public const WARNING_USER_SUSPENDED = array("status" => ResponseStatus::WARNING, "verbose" => "Authentication failed : User is suspended.", "code" => 403);

    //ERREURS
    public const ERROR_INVALID_USER_CREDENTIALS = array("status" => ResponseStatus::ERROR, "verbose" => "Authentication failed : Invalid user credentials.", "code" => 403);


    //--
    //VERIFY_TOKEN
    //--

    //SUCCES
    public const SUCCESS_VALID_TOKEN = array("status" => ResponseStatus::SUCCESS, "verbose" => "Authentication successful : JWT Token is valid.", "code" => 200);

    //ERREURS
    public const ERROR_INVALID_TOKEN = array("status" => ResponseStatus::ERROR, "verbose" => "Authentication failed : Invalid JWT Token.", "code" => 401);


    //--
    //PROJECT_CREATE
    //--

    //SUCCES
    public const SUCCESS_PROJECT_CREATED = array("status" => ResponseStatus::SUCCESS, "verbose" => "Project created.", "code" => 200);

    //ERREURS
    public const ERROR_NAME_USED = array("status" => ResponseStatus::ERROR, "verbose" => "Project name already in use.", "code" => 400);


}

?>