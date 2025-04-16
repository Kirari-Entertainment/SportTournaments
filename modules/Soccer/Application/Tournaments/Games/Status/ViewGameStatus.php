<?php namespace App\Soccer\Application\Tournaments\Games\Status;

use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class ViewGameStatus extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(
        string $gameId
    ): GameStatus {
        static::preventEmptyStringParams(
            $gameId
        );

        $game = $this->recordsBook->findGame($gameId) ?? static::throwEntityNotFound('Game');

        return new GameStatus(
            $game->getId(),
            $game->getScheduledFor()->format('Y-m-d H:i:s'),
            $game->getScheduledFor()->format('Y-m-d H:i:s'),
            $game->getStatus()->name,
            $game->getTeamA()->getId(),
            $game->getTeamB()->getId(),
            $game->getTeamA()->getName(),
            $game->getTeamB()->getName(),
            $game->getScoreTeamA(),
            $game->getScoreTeamB()
        );
    }
}