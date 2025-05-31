<?php namespace App\Soccer\Application\Tournaments\Games\Status;

class GameStatus {
    public function __construct(
        public string $id,
        public string $startedAt,
        public string $endedAt,
        public string $status,
        public string $contenantAId,
        public string $contenantBId,
        public string $contenantAName,
        public string $contenantBName,
        public int $contenantAScore,
        public int $contenantBScore
    ) { }
}