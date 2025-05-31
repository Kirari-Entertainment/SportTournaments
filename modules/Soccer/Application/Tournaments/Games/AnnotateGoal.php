<?php namespace App\Soccer\Application\Tournaments\Games;

use App\Soccer\Domain\Game\GameStatus;
use App\Soccer\Domain\Game\Goal;
use App\Soccer\Domain\RecordsBook;
use DateTime;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class AnnotateGoal extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(
        string $gameId,
        string $teamId,
        string $playerId
    ) : true {
        static::preventEmptyStringParams(
            $gameId,
            $teamId,
            $playerId
        );

        $game = $this->recordsBook->findGame($gameId) ?? static::throwEntityNotFound('Game');
        $team = $this->recordsBook->findTeam($teamId) ?? static::throwEntityNotFound('Team');
        $player = $this->recordsBook->findPlayer($playerId) ?? static::throwEntityNotFound('Player');

        if ($game->getStatus() !== GameStatus::IN_PROGRESS) {
            static::throwInvalidParameter('Game has not started yet');
        }

        $goal = new Goal(
            $gameId,
            $teamId,
            $playerId,
            new DateTime()
        );

        $this->recordsBook->annotateGoal($goal);

        return true;
    }
}