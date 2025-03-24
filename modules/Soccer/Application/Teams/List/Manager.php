<?php namespace App\Soccer\Application\Teams\List;

use App\Soccer\Domain\RecordsBook;

class Manager {
    public function __construct(
        private RecordsBook $RecordsBook
    ) { }

    public function execute() : TeamsList {
        $teamsList = new TeamsList();
        $allTeams = $this->RecordsBook->retrieveAllTeams();

        foreach ($allTeams as $team) {
            $teamsList[] = new TeamListEntry(
                $team->getId(),
                $team->getName()
            );
        }
        return $teamsList;
    }
}