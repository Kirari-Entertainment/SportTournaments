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

                SimpleRouter::post('{tournamentId}/teams', 'Tournaments@registerTeam');
                SimpleRouter::get('{tournamentId}/teams', 'Tournaments@listRegisteredTeams');
                SimpleRouter::post('{tournamentId}/team/{teamId}/members', 'Tournaments@addTeamMember');
                SimpleRouter::get('{tournamentId}/team/{teamId}/members', 'Tournaments@listTeamMembers');

                SimpleRouter::post('{tournamentId}/games', 'Tournaments@scheduleGame');
                SimpleRouter::get('{tournamentId}/games', 'Tournaments@listGames');
            }
        );

        SimpleRouter::group([
            'namespace' => 'App\Soccer\Infrastructure\APIControllers',
            'prefix' => '/soccer/game'
        ],

            function() {
                SimpleRouter::get('{gameId}', 'Games@status');
                SimpleRouter::post('{gameId}/start', 'Games@start');
                SimpleRouter::post('{gameId}/goals', 'Games@annotateGoal');
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
            'prefix' => '/soccer/player'
        ],
            function() {
                SimpleRouter::post('', 'Players@register');
                SimpleRouter::get('', 'Players@index');
                SimpleRouter::get('{playerId}', 'Players@show');
                SimpleRouter::get('{playerId}/picture', 'Players@showProfPic');
                SimpleRouter::post('{playerId}/picture', 'Players@setProfPic');
            }
        );
    }
}