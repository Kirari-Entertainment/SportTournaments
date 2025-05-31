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

        $allGoals = $this->recordsBook->retrieveAllGoalsByGame($gameId);

        $teamAGoals = 0;
        $teamBGoals = 0;

        foreach ($allGoals as $goal) {
            if ($goal->getTeamId() === $game->getTeamA()->getId())
                ++$teamAGoals;
            elseif ($goal->getTeamId() === $game->getTeamB()->getId())
                ++$teamBGoals;
        }

        return new GameStatus(
            $game->getId(),
            $game->getScheduledFor()->format('Y-m-d H:i:s'),
            $game->getScheduledFor()->format('Y-m-d H:i:s'),
            $game->getStatus()->name,
            $game->getTeamA()->getId(),
            $game->getTeamB()->getId(),
            $game->getTeamA()->getName(),
            $game->getTeamB()->getName(),
            $teamAGoals,
            $teamBGoals
        );
    }
}