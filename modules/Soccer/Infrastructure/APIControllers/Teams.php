<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Teams\AddExistingMember\AddExistingMember as AddExistingMemberManager;
use App\Soccer\Application\Teams\AddUnregisteredMember\AddUnregisteredMember as AddUnregisteredMemberManager;
use App\Soccer\Application\Teams\Register\RegisterTeam as RegisterManager;
use App\Soccer\Application\Teams\List\ListTeams as ListTeamsManager;
use App\Soccer\Application\Teams\List\TeamsList;
use App\Soccer\Domain\RecordsBook;
use Robust\Auth\Roles;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\HTTP\API\DefaultController;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\Infrastructure\Provider as InfProvider;

class Teams extends DefaultController {
    public static function register() : void {
        static::executeAuthenticated(
            managedUseCase: fn() => (new RegisterManager(
                InfProvider::requestEntity(IdGenerator::class, [ 'type' => 'uuid' ]),
                InfProvider::requestEntity(RecordsBook::class)
            ))->execute(...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'string' => RCODES::Created ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }

    public static function index() : void {
        

        static::executeAsGuest(
            managedUseCase: fn() => (new ListTeamsManager(
                InfProvider::requestEntity(RecordsBook::class)
            ))->execute(),

            resultCodes: [ TeamsList::class => RCODES::OK ]
        );
    }

    public static function addMemberForTournament(string $tournamentId, string $teamId) : void {
        $request = static::parseJsonInputFromCurrentRequest();
        $useCase = isset($request['playerId']) ? new AddExistingMemberManager(
            InfProvider::requestEntity(IdGenerator::class, [ 'type' => 'uuid' ]),
            InfProvider::requestEntity(RecordsBook::class)
        ) : new AddUnregisteredMemberManager(
            InfProvider::requestEntity(IdGenerator::class, [ 'type' => 'uuid' ]),
            InfProvider::requestEntity(RecordsBook::class)
        );

        static::executeAuthenticated(
            managedUseCase: fn() => $useCase->execute(
                $tournamentId,
                $teamId,
                ...$request
            ),

            resultCodes: [ 'string' => RCODES::Created ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }
}