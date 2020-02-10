<?php

/**
 * Fonction qui retourne une chaîne de caractères aléatoire de longueur n.
 *
 * @param int           $n                  -   Longueur de la chaîne a génerer
 *
 * @return string
 */
function randomString($n) : string {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $n; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>