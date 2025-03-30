<?php namespace App\Soccer\Domain\Tournament;

use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Team\Team;

class TeamMembership {
    public function __construct(
        private Tournament      $tournament,
        private Team            $team,
        private Player          $player
    ) {}

    public function getId(): string { return $this->tournament->getId() . $this->team->getId() . $this->player->getId(); }
    public function getTournament(): Tournament { return $this->tournament; }
    public function getPlayer(): Player { return $this->player; }
    public function getTeam(): Team { return $this->team; }
    public function setTeam(Team $newTeam): void { $this->team = $newTeam; }
}