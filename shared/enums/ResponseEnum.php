<?php

abstract class ResponseEnum {

    //--
    //GENERIQUE / COMMUN
    //--

    //DEBUG
    public const DEBUG_RESPONSE_SUCCESS = array("status" => ResponseStatus::SUCCESS, "code" => "DEBUG_RESPONSE_SUCCESS", "verbose" => "Debug : Success.", "http-code" => 200);
    public const DEBUG_RESPONSE_WARNING = array("status" => ResponseStatus::WARNING, "code" => "DEBUG_RESPONSE_WARNING", "verbose" => "Debug : Warning.", "http-code" => 400);
    public const DEBUG_RESPONSE_ERROR = array("status" => ResponseStatus::ERROR, "code" => "DEBUG_RESPONSE_ERROR", "verbose" => "Debug : Error.", "http-code" => 400);

    //SUCCES

    //ERREURS
    public const ERROR_MISSING_ARGUMENT = array("status" => ResponseStatus::ERROR, "code" => "ERROR_MISSING_ARGUMENT", "verbose" => "Missing required argument(s).", "http-code" => 400);
    public const ERROR_INVALID_ARGUMENT = array("status" => ResponseStatus::ERROR, "code" => "ERROR_INVALID_ARGUMENT", "verbose" => "Invalid stated argument(s)", "http-code" => 400);
    public const ERROR_ACCESS_DENIED = array("status" => ResponseStatus::ERROR, "code" => "ERROR_ACCESS_DENIED", "verbose" => "Token is invalid, or insufficient permissions", "http-code" => 403);
    public const ERROR_ENTITY_NOT_FOUND = array("status" => ResponseStatus::ERROR, "code" => "ERROR_ENTITY_NOT_FOUND", "verbose" => "Entity was not found", "http-code" => 404);


    //--
    //AUTH
    //--

    //SUCCES
    public const SUCCESS_AUTHENTICATED = array("status" => ResponseStatus::SUCCESS, "code" => "SUCCESS_AUTHENTICATED", "verbose" => "Authentication successful and JWT generated.", "http-code" => 200);

    //AVERTISSEMENTS
    public const WARNING_USER_SUSPENDED = array("status" => ResponseStatus::WARNING, "code" => "WARNING_USER_SUSPENDED", "verbose" => "Authentication failed : User is suspended.", "http-code" => 403);

    //ERREURS
    public const ERROR_INVALID_USER_CREDENTIALS = array("status" => ResponseStatus::ERROR, "code" => "ERROR_INVALID_USER_CREDENTIALS", "verbose" => "Authentication failed : Invalid user credentials.", "http-code" => 403);


    //--
    //VERIFY_TOKEN
    //--

    //SUCCES
    public const SUCCESS_VALID_TOKEN = array("status" => ResponseStatus::SUCCESS, "code" => "SUCCESS_VALID_TOKEN", "verbose" => "Authentication successful : JWT Token is valid.", "http-code" => 200);

    //ERREURS
    public const ERROR_INVALID_TOKEN = array("status" => ResponseStatus::ERROR, "code" => "ERROR_INVALID_TOKEN", "verbose" => "Authentication failed : Invalid JWT Token.", "http-code" => 401);


    //--
    //PROJECT
    //--

    //SUCCES
    public const SUCCESS_PROJECT_CREATED = array("status" => ResponseStatus::SUCCESS, "code" => "SUCCESS_PROJECT_CREATED", "verbose" => "Project created.", "http-code" => 200);

    //ERREURS
    public const ERROR_NAME_USED = array("status" => ResponseStatus::ERROR, "code" => "ERROR_NAME_USED", "verbose" => "Project name already in use.", "http-code" => 400);


    //--
    //HUMAN_RESOURCE
    //--

    //SUCCES
    public const SUCCESS_HUMAN_RESOURCE_CREATED = array("status" => ResponseStatus::SUCCESS, "code" => "SUCCESS_HUMAN_RESOURCE_CREATED", "verbose" => "Human resource created.", "http-code" => 200);

    //ERREURS
    public const ERROR_EMAIL_USED = array("status" => ResponseStatus::ERROR, "code" => "ERROR_EMAIL_USED", "verbose" => "E-mail adress already in use.", "http-code" => 400);
    public const ERROR_RESOURCE_ALREADY_LINKED = array("status" => ResponseStatus::ERROR, "code" => "ERROR_RESOURCE_ALREADY_LINKED", "verbose" => "Target resource already exists and is already linked.", "http-code" => 400);
    

    //--
    //TASK
    //--

    //SUCCES
    public const SUCCESS_TASK_CREATED = array("status" => ResponseStatus::SUCCESS, "code" => "SUCCESS_TASK_CREATED", "verbose" => "Task created.", "http-code" => 200);
}

?>