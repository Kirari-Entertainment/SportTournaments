<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Players\List\ListPlayers;
use App\Soccer\Application\Players\List\PlayersList;
use App\Soccer\Application\Players\Register\RegisterPlayer;
use App\Soccer\Domain\RecordsBook;
use Robust\Auth\Roles;
use Robust\Boilerplate\HTTP\API\DefaultController;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\Infrastructure\Provider;

class Players extends DefaultController {
    public static function index() {
        static::executeAsGuest(
            managedUseCase: fn() => (new ListPlayers(
                Provider::requestEntity(RecordsBook::class)
            ))->execute(),

            resultCodes: [ PlayersList::class => RCODES::OK ]
        );
    }

    public static function register() {
        static::executeAuthenticated(
            managedUseCase: fn() => (new RegisterPlayer(
                Provider::requestEntity(IdGenerator::class, ['type' => 'uuid']),
                Provider::requestEntity(RecordsBook::class)
            ))->execute(...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'string' => RCODES::Created ],

            authorizedRoles: [ Roles::Manager ]
        );

    }
}