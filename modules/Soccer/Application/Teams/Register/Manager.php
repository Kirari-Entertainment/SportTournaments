<?php namespace App\Soccer\Application\Teams\Register;

use App\Soccer\Domain\Team\Entity as Team;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;

class Manager {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $RecordsBook
    ) { }

    public function execute(string $name) : string {
        $newTeamId = $this->idGenerator->nextForClass(Team::class);

        $newTeam = new Team($newTeamId, $name);
        $this->RecordsBook->registerTeam($newTeam);

        return $newTeamId;
    }
}