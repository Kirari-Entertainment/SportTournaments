<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Tournaments\Register\Manager as RegisterManager;
use App\Soccer\Application\Tournaments\List\Manager as ListTournamentsManager;
use App\Soccer\Application\Tournaments\List\TournamentsList;
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
}