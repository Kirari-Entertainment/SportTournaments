<?php namespace App\Soccer\Domain\Player;

use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\Tournament;

class TeamMembership {
    public function __construct(
        private string $id,
        private Tournament $tournament,
        private Player $player,
        private Team $team
    ) {}

    public function getId(): string { return $this->id; }
    public function getTournament(): Tournament { return $this->tournament; }
    public function getPlayer(): Player { return $this->player; }
    public function getTeam(): Team { return $this->team; }
}