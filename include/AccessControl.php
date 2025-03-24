<?php namespace Robust\Boilerplate;

class AccessControl {
    public static function setOrigins(string $origin = 'any') : void {
        // Define qué aplicaciones tienen derecho a consumir el Endpoints
        header('Access-Control-Allow-Headers: Authorization, Origin, Content-Type, Accept');

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if ($origin === 'any') {
                $origin = $_SERVER['HTTP_ORIGIN'];
            }

            header("Access-Control-Allow-Origin: {$origin}");
            header('Access-Control-Allow-Credentials: true');
        }

    }

    public static function confirmPreflight() : void {
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }
}
