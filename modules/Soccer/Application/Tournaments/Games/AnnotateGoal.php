<?php namespace App\Soccer\Application\Tournaments\Games;

use App\Soccer\Domain\Game\GameStatus;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class AnnotateGoal extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(
        string $gameId,
        string $teamSide,
        string $playerId
    ) : true {
        static::preventEmptyStringParams(
            $gameId,
            $teamSide,
            $playerId
        );

        $game = $this->recordsBook->findGame($gameId) ?? static::throwEntityNotFound('Game');
        $player = $this->recordsBook->findPlayer($playerId) ?? static::throwEntityNotFound('Player');

        if ($teamSide !== 'A' && $teamSide !== 'B') {
            static::throwInvalidParameter('Team side must be A or B');
        }

        if ($game->getStatus() !== GameStatus::IN_PROGRESS) {
            static::throwInvalidParameter('Game has not started yet');
        }

        if ($teamSide === 'A') {

            $game->annotateGoalForTeamA($player);
        } else {
            $game->annotateGoalForTeamB($player);
        }

        $this->recordsBook->updateGame($game);

        return true;
    }
}