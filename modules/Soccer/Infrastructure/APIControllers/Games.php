<?php namespace App\Soccer\Infrastructure\APIControllers;

use App\Soccer\Application\Tournaments\Games\AnnotateGoal;
use App\Soccer\Application\Tournaments\Games\StartGame;
use App\Soccer\Application\Tournaments\Games\Status\GameStatus;
use App\Soccer\Application\Tournaments\Games\Status\ViewGameStatus;
use App\Soccer\Domain\RecordsBook;
use Robust\Auth\Roles;
use Robust\Boilerplate\HTTP\API\DefaultController;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\Infrastructure\Provider;

class Games extends DefaultController {
    public function status(string $gameId) {
        static::executeAsGuest(
            managedUseCase: fn() => (new ViewGameStatus(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($gameId),

            resultCodes: [ GameStatus::class => RCODES::OK ]
        );
    }

    public function start(string $gameId) {
        static::executeAuthenticated(
            managedUseCase: fn() => (new StartGame(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($gameId),

            resultCodes: [ 'boolean' => RCODES::OK ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }

    public function annotateGoal(string $gameId) {
        static::executeAuthenticated(
            managedUseCase: fn() => (new AnnotateGoal(
                Provider::requestEntity(RecordsBook::class)
            ))->execute($gameId, ...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'boolean' => RCODES::OK ],

            authorizedRoles: [
                Roles::Manager
            ]
        );
    }
}