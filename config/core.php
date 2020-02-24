<?php

$jwtConfig = array("iss" => "https://api.ythepaut.com/g4",
                    "iat" => time(),
                    "nbf" => time(),
                    "req-exp" => time() + 900,
                    "ren-exp" => time() + 604800,
                    "key" => "H5MHz4rgWwUs7nD9e6D8PTYUNCeXw3xQ");

?>