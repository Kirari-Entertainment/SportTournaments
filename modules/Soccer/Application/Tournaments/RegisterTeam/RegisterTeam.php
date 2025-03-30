<?php namespace App\Soccer\Application\Tournaments\RegisterTeam;

use App\Soccer\Domain\RecordsBook;

class RegisterTeam {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }
    
    public function execute(
        string $tournamentId,
        string $teamId
    ) : true {
        $tournament = $this->recordsBook->findTournament($tournamentId);
        if ($tournament === null) {
            throw new \InvalidArgumentException('Tournament not found');
        }

        $team = $this->recordsBook->findTeam($teamId);
        if ($team === null) {
            throw new \InvalidArgumentException('Team not found');
        }

        $tournament->registerTeam($team);

        $this->recordsBook->updateTournament($tournament);

        return true;

    }
}