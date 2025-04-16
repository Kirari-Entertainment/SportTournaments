<?php namespace App\Soccer\Application\Tournaments\Games;

use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class StartGame extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $gameId
    ) : true {
        static::preventEmptyStringParams(
            $gameId
        );

        $game = $this->recordsBook->findGame($gameId) ?? static::throwEntityNotFound('Game');

        $game->startGame();

        $this->recordsBook->updateGame($game);

        return true;
    }
}