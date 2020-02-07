<?php

abstract class ResponseEnum {

    //--
    //GENERIQUE
    //--

    //DEBUG
    public const DEBUG_RESPONSE_SUCCESS = array("status" => ResponseStatus::SUCCESS, "verbose" => "Debug : Success.");
    public const DEBUG_RESPONSE_WARNING = array("status" => ResponseStatus::WARNING, "verbose" => "Debug : Warning.");
    public const DEBUG_RESPONSE_ERROR = array("status" => ResponseStatus::ERROR, "verbose" => "Debug : Error.");

    //ERREURS
    public const ERROR_MISSING_ARGUMENT = array("status" => ResponseStatus::ERROR, "verbose" => "Missing required argument(s).");


    //--
    //AUTH
    //--

    //ERREURS
    public const ERROR_INVALID_USER_CREDENTIALS = array("status" => ResponseStatus::ERROR, "verbose" => "Invalid user credentials.");


}

?>