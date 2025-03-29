<?php namespace App\Soccer\Application\Tournaments\List;

use App\Soccer\Domain\RecordsBook;

class Manager {
    public function __construct(
        private RecordsBook $recordsBook
    ) {}

    public function execute() : TournamentsList {
        $allTournaments = $this->recordsBook->retrieveAllTournaments();
        $tournamentsList = new TournamentsList();
        
        foreach ($allTournaments as $tournament) {
            $tournamentsList[] = new TournamentListEntry(
                $tournament->getId(),
                $tournament->getName()
            );
        }

        return $tournamentsList;
    }
}