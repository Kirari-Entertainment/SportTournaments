<?php namespace App\Soccer\Domain\Game;

use App\Soccer\Domain\Player\Player;

class Goal {
    public function __construct(
        private string $gameId,
        private string $teamId,
        private ?string $playerId,
        private ?\DateTime $scoredAt,
    ) { }

    public function getGameId(): string { return $this->gameId; }
    public function getTeamId(): string { return $this->teamId; }
    public function getPlayerId(): ?string { return $this->playerId; }
    public function getScoredAt(): ?\DateTime { return $this->scoredAt; }
}