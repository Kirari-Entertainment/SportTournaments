<?php namespace App\Soccer;

use Pecee\SimpleRouter\SimpleRouter;

class Endpoints {
    public static function open() : void {
        SimpleRouter::group([
            'namespace' => 'App\Soccer\Infrastructure\APIControllers',
            'prefix' => '/soccer/tournament'
        ],
            function() {
                SimpleRouter::post('', 'Tournaments@register');
                SimpleRouter::get('', 'Tournaments@index');

                SimpleRouter::post('{tournamentId}/teams', 'Tournaments@addTeam');
                SimpleRouter::get('{tournamentId}/teams', 'Tournaments@listTeams');
                SimpleRouter::post('{tournamentId}/teams/{teamId}/members', 'Tournament@addTeamMember');
                SimpleRouter::get('{tournamentId}/teams/{teamId}/members', 'Tournament@listTeamMembers');
            }
        );

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
            'prefix' => '/soccer/players'
        ],
            function() {
                SimpleRouter::post('', 'Players@register');
                SimpleRouter::get('', 'Players@index');
            }
        );
    }
}