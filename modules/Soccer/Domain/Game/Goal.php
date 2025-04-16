<?php namespace App\Soccer\Domain\Game;

use App\Soccer\Domain\Player\Player;

class Goal {
    public function __construct(
        private ?Player $player,
        private ?\DateTime $scoredAt,
    ) { }

    public function getPlayer(): ?Player { return $this->player; }
    public function getScoredAt(): ?\DateTime { return $this->scoredAt; }
}