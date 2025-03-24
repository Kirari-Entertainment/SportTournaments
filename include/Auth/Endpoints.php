<?php namespace Robust\Auth;

// Puntos de acceso habilitados
use Pecee\SimpleRouter\SimpleRouter;

class Endpoints {
    public static function open() : void {
        SimpleRouter::group(['namespace' => 'Robust\Auth', 'prefix' => '/auth'],
            function() {
                SimpleRouter::post('/signup', 'APIAuthController@signup');
                SimpleRouter::post('/login', 'APIAuthController@login');
                SimpleRouter::get('/user', 'APIAuthController@index');
                // Declaración que rechaza todos los métodos no especificados
                SimpleRouter::all('/signup', 'APIAuthController@unallowedMethod');
                SimpleRouter::all('/login', 'APIAuthController@unallowedMethod');
            }
        );
    }
}
