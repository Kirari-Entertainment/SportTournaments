<?php namespace App\Soccer\Application\Tournaments\ListScheduledGames;

readonly class GamesListEntry {
    public function __construct(
        public string $gameId,
        public string $scheduledAt,
        public string $contenants,
        public string $contenantAId,
        public string $contenantBId
    ) { }
}