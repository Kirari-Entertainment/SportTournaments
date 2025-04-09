<?php namespace App\Soccer\Application\Tournaments\ScheduleGame;

use App\Soccer\Domain\Game\Game;
use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Tournament\Tournament;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class ScheduleGame extends InteractorWithUtils {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $tournamentId,
        string $scheduledFor,
        string $teamAId,
        string $teamBId
    ) : string {
        static::preventEmptyStringParams(
            $tournamentId,
            $scheduledFor,
            $teamAId,
            $teamBId
        );

        $tournament = $this->recordsBook->findTournament($tournamentId) ?? static::throwEntityNotFound('Tournament');
        $teamA = $this->recordsBook->findTeam($teamAId) ?? static::throwEntityNotFound('Team A');
        $teamB = $this->recordsBook->findTeam($teamBId) ?? static::throwEntityNotFound('Team B');
        
        // 2. Domain validation
        $this->validateTeamsRegisteredInTournament($tournament, $teamA, $teamB);
        $this->validateGameDate($tournament, new \DateTime($scheduledFor));
        
        // 3. Create the game entity
        $gameId = $this->idGenerator->nextForClass(Game::class);
        $game = new Game(
            $gameId,
            $tournament,
            new \DateTime($scheduledFor),
            $teamA, 
            $teamB
        );
        
        // 4. Persist
        $this->recordsBook->registerGame($game);
        
        return $gameId;
    }
    
    private function validateTeamsRegisteredInTournament(Tournament $tournament, $teamA, $teamB): void {
        $registeredTeams = $tournament->getRegisteredTeams();
        $teamARegistered = false;
        $teamBRegistered = false;
        
        foreach ($registeredTeams as $team) {
            if ($team->getId() === $teamA->getId()) $teamARegistered = true;
            if ($team->getId() === $teamB->getId()) $teamBRegistered = true;
        }
        
        if (!$teamARegistered || !$teamBRegistered) {
            throw new UseCaseException(
                'Teams must be registered in the tournament',
                UseCaseException::$INVALID_PARAMETER
            );
        }
        
        if ($teamA->getId() === $teamB->getId()) {
            throw new UseCaseException(
                'A team cannot play against itself',
                UseCaseException::$INVALID_PARAMETER
            );
        }
    }
    
    private function validateGameDate(Tournament $tournament, \DateTime $gameDate): void {
        if ($gameDate < $tournament->getStartDate() || $gameDate > $tournament->getEndDate()) {
            throw new UseCaseException(
                'Game must be scheduled within tournament dates',
                UseCaseException::$INVALID_PARAMETER
            );
        }
    }
}