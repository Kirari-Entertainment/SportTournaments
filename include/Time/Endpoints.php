<?php namespace Robust\Time;

// Puntos de accesso habilitados
use Pecee\SimpleRouter\SimpleRouter;
class Endpoints {
    public static function open() : void {
        SimpleRouter::group(['namespace' => 'Robust\Time', 'prefix' => '/time'],
            function() {
                SimpleRouter::get('', 'APIController@index');
                SimpleRouter::get('/{elem}', 'APIController@show');
                // Declaración que rechaza todos los métodos no especificados
                SimpleRouter::all('', 'APIController@unallowedMethod');
            }
        );
    }
}