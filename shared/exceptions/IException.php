<?php

interface IException {

    public function getMessage();                 // Message d'erreur
    public function getCode();                    // Code d'érreur (cf. documentation)
    public function getFile();                    // Fichier qui a causé l'exception
    public function getLine();                    // Ligne qui a causé l'exception
    public function getTrace();                   // Stacktrace (liste)
    public function getTraceAsString();           // Stacktrace (string)
   
    /* Fonctions à implémenter et surcharger */
    public function __construct($message = null, $code = 0);
    public function __toString();

}

?>