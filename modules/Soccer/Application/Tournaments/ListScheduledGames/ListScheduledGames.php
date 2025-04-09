<?php namespace App\Soccer\Application\Tournaments\ListScheduledGames;

use App\Soccer\Domain\Game\GameStatus;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class ListScheduledGames extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(string $tournamentId): GamesList {
        // Get only scheduled games for this tournament
        $games = $this->recordsBook->retrieveGamesByTournamentAndStatus(
            $tournamentId, 
            GameStatus::SCHEDULED
        );
        
        // Sort games by date
        usort($games, function ($a, $b) {
            return $a->getScheduledFor() <=> $b->getScheduledFor();
        });
        
        // Convert to response objects
        $scheduledGames = new GamesList();
        foreach ($games as $game) {
            $scheduledGames[] = new GamesListEntry(
                $game->getId(),
                $game->getScheduledFor()->format('Y-m-d H:i:s'),
                "{$game->getTeamA()->getName()} vs {$game->getTeamB()->getName()}",
            );
        }

        return $scheduledGames;
    }
}