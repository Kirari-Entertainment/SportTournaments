<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Teams\Register\Manager as RegisterManager;
use App\Soccer\Application\Teams\List\Manager as ListTeamsManager;
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


}