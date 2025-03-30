<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Tournaments\ListTeamMemberships\ListTeamMemberships;
use App\Soccer\Application\Tournaments\ListTeamMemberships\TeamMembersList;
use App\Soccer\Application\Tournaments\Register\Manager as RegisterManager;
use App\Soccer\Application\Tournaments\List\ListTournaments as ListTournamentsManager;
use App\Soccer\Application\Tournaments\List\TournamentsList;
use App\Soccer\Application\Tournaments\ListRegisteredTeams\ListRegisteredTeams;
use App\Soccer\Application\Tournaments\ListRegisteredTeams\RegisteredTeamsList;
use App\Soccer\Application\Tournaments\RegisterTeam\RegisterTeam;
use App\Soccer\Application\Tournaments\RegisterTeamMembership\RegisterTeamMembership;
use App\Soccer\Application\Tournaments\RegisterTeamMembership\RegisterTeamMembershipOfNewPlayer;
use App\Soccer\Domain\RecordsBook;
use Robust\Auth\Roles;
use Robust\Boilerplate\HTTP\API\DefaultController;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\Infrastructure\Provider;

class Tournaments extends DefaultController {
    public function register() {
        static::executeAuthenticated(
            managedUseCase: fn() => (new RegisterManager(
                Provider::requestEntity(IdGenerator::class, ['type' => 'uuid']),
                Provider::requestEntity(RecordsBook::class)
            ))->execute(...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'string' => RCODES::Created ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }

    public function index() {
        static::executeAsGuest(
            managedUseCase: fn() => (new ListTournamentsManager(
                Provider::requestEntity(RecordsBook::class)
            ))->execute(),

            resultCodes: [ TournamentsList::class => RCODES::OK ]
        );
    }

    public function registerTeam(string $tournamentId) {
        static::executeAuthenticated(
            managedUseCase: fn() => (new RegisterTeam(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($tournamentId, ...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'boolean' => RCODES::OK ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }

    public function ListRegisteredTeams(string $tournamentId) {
        static::executeAsGuest(
            managedUseCase: fn() => (new ListRegisteredTeams(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($tournamentId),

            resultCodes: [ RegisteredTeamsList::class => RCODES::OK ]
        );
    }

    public static function addTeamMember(string $tournamentId, string $teamId) {
        $bodyParams = static ::parseJsonInputFromCurrentRequest();

        if (isset($bodyParams['playerId'])) {
            $useCase = new RegisterTeamMembership(
                Provider::requestEntity(RecordsBook::class)
            );
        } else {
            $useCase = new RegisterTeamMembershipOfNewPlayer(
                Provider::requestEntity(IdGenerator::class, ['type' => 'uuid']),
                Provider::requestEntity(RecordsBook::class)
            );
        }

        static::executeAuthenticated(
            managedUseCase: fn() => $useCase->execute($tournamentId, $teamId, ...$bodyParams),

            resultCodes: [ 'boolean' => RCODES::OK ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }

    public static function listTeamMembers(string $tournamentId, string $teamId) {
        static::executeAsGuest(
            managedUseCase: fn() => (new ListTeamMemberships(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($tournamentId, $teamId),

            resultCodes: [ TeamMembersList::class => RCODES::OK ]
        );
    }
}