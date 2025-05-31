<?php namespace App\Soccer\Domain\Game;

use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\Tournament;

class Game {
    private GameStatus $status;

    public function __construct(
        private string $id,
        private Tournament $tournament,
        private \DateTime $scheduledFor,
        private Team $teamA,
        private Team $teamB
    ) {
        $this->status = GameStatus::SCHEDULED;
    }

    // Getters for all properties
    public function getId(): string { return $this->id; }
    public function getTournament(): Tournament { return $this->tournament; }
    public function getScheduledFor(): \DateTime { return $this->scheduledFor; }
    public function getTeamA(): Team { return $this->teamA; }
    public function getTeamB(): Team { return $this->teamB; }
    public function getStatus(): GameStatus { return $this->status; }
    
    // Game state management
    public function startGame(): void {
        $this->status = GameStatus::IN_PROGRESS;
    }
    
    public function finishGame(): void {
        $this->status = GameStatus::FINISHED;
    }
    
    public function setStatus(GameStatus $status): void {
        $this->status = $status;
    }
    
}