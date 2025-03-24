<?php namespace App\Soccer;

use Pecee\SimpleRouter\SimpleRouter;

class Endpoints {
    public static function open() : void {
        SimpleRouter::group([
            'namespace' => 'App\Soccer\Infrastructure\APIControllers',
            'prefix' => '/soccer/team'
        ],
            function() {
                SimpleRouter::post('', 'Teams@register');
                SimpleRouter::get('', 'Teams@index');
            }
        );

        SimpleRouter::group([
            'namespace' => 'App\Soccer\Infrastructure\APIControllers',
            'prefix' => '/soccer/tournament'
        ],
            function() {
                SimpleRouter::post('', 'Tournaments@register');
            }
        );

    }
}