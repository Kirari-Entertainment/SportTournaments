<?php namespace App\Soccer\Application\Tournaments\ListRegisteredTeams;

use App\Soccer\Domain\RecordsBook;

readonly  class ListRegisteredTeams {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }
    
    public function execute(
        string $tournamentId
    ): RegisteredTeamsList {
        $tournament = $this->recordsBook->findTournament($tournamentId);
        if ($tournament === null) {
            throw new \InvalidArgumentException('Tournament not found');
        }

        $teams = new RegisteredTeamsList();
        foreach ($tournament->getRegisteredTeams() as $team) {
            $teams[] = new RegisteredTeamsListEntry(
                $team->getId(),
                $team->getName()
            );
        }

        return $teams;
    }
}