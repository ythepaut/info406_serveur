<?php

class Autoloader {

    static function register() : void {
        spl_autoload_register(array(__CLASS__, "autoload"));
    }


    /**
     * Fonction qui charge la classe $className
     * 
     * @param                           $className          -   Nom de la classe à importer
     * 
     * @return void
     * 
     * @throws ClassNotFoundException   La classe demandée n'a pas été trouvée
     */
    static function autoload(string $className) : void {
        
        $files = array(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . $className . ".php",
                       dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "objects" . DIRECTORY_SEPARATOR . $className . ".php",
                       dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "enums" . DIRECTORY_SEPARATOR . $className . ".php",
                       dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "exceptions" . DIRECTORY_SEPARATOR . $className . ".php",
                       dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR . $className . ".php",
                       dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR . "jwt" . DIRECTORY_SEPARATOR . $className . ".php");
        
        $found = false;

        foreach ($files as $file) {

            if (is_readable($file)) {
                require_once($file);
                $found = true;
            }

        }
        
        if (!$found) {
            throw new ClassNotFoundException("Class " . $className . " could not be imported.", 1);
        }

    }

}

?>