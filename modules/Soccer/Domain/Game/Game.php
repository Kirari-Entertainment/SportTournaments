<?php namespace App\Soccer\Domain\Game;

use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\Tournament;

class Game {
    private GameStatus $status;
    private array $teamAGoals = [];
    private array $teamBGoals = [];

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
    public function getTeamAGoals(): array { return $this->teamAGoals; }
    public function getTeamBGoals(): array { return $this->teamBGoals; }
    
    // Game state management
    public function startGame(): void {
        $this->status = GameStatus::IN_PROGRESS;
    }
    
    public function finishGame(): void {
        $this->status = GameStatus::FINISHED;
    }
    
    // Live goal annotation
    public function annotateGoalForTeamA(Player $player): void {
        if ($this->status !== GameStatus::IN_PROGRESS) {
            throw new \InvalidArgumentException('Cannot annotate goals for games not in progress');
        }
        $this->teamAGoals[] = new Goal($player, new \DateTime());
    }
    
    public function annotateGoalForTeamB(Player $player): void {
        if ($this->status !== GameStatus::IN_PROGRESS) {
            throw new \InvalidArgumentException('Cannot annotate goals for games not in progress');
        }
        $this->teamBGoals[] = new Goal($player, new \DateTime());
    }
    
    // Special methods for repository use (reconstructing state)
    public function setTeamAGoals(array $goals): void {
        // Make sure all elements are Goal objects
        foreach ($goals as $goal) {
            if (!($goal instanceof Goal)) {
                throw new \InvalidArgumentException('All elements must be Goal objects');
            }
        }
        $this->teamAGoals = $goals;
    }
    
    public function setTeamBGoals(array $goals): void {
        // Make sure all elements are Goal objects
        foreach ($goals as $goal) {
            if (!($goal instanceof Goal)) {
                throw new \InvalidArgumentException('All elements must be Goal objects');
            }
        }
        $this->teamBGoals = $goals;
    }
    
    public function setStatus(GameStatus $status): void {
        $this->status = $status;
    }
    
    // Game statistics
    public function getScoreTeamA(): int {
        return count($this->teamAGoals);
    }
    
    public function getScoreTeamB(): int {
        return count($this->teamBGoals);
    }
    
    public function getWinner(): ?Team {
        if ($this->status !== GameStatus::FINISHED) {
            return null;
        }
        
        if ($this->getScoreTeamA() > $this->getScoreTeamB()) {
            return $this->teamA;
        } elseif ($this->getScoreTeamB() > $this->getScoreTeamA()) {
            return $this->teamB;
        }
        
        return null; // Draw
    }
    
    /**
     * Factory method to create a game from historical data
     * This is used by the repository to reconstruct games from the database
     */
    public static function createFromHistoricalData(
        string $id,
        Tournament $tournament,
        \DateTime $scheduledFor,
        Team $teamA, 
        Team $teamB,
        GameStatus $status,
        array $teamAGoals = [],
        array $teamBGoals = []
    ): self {
        // Validate goals arrays
        foreach ($teamAGoals as $goal) {
            if (!($goal instanceof Goal)) {
                throw new \InvalidArgumentException('All team A goals must be Goal objects');
            }
        }
        
        foreach ($teamBGoals as $goal) {
            if (!($goal instanceof Goal)) {
                throw new \InvalidArgumentException('All team B goals must be Goal objects');
            }
        }
        
        // Create base game instance
        $game = new self($id, $tournament, $scheduledFor, $teamA, $teamB);
        
        // Set retrieved state
        $game->status = $status;
        $game->teamAGoals = $teamAGoals;
        $game->teamBGoals = $teamBGoals;
        
        return $game;
    }
}