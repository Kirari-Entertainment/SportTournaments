<?php namespace App\Soccer\Application\Teams\Register;

use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\UseCaseException;

class Manager {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $RecordsBook
    ) { }

    public function execute(string $name, ?string $captainId = null) : string {
        $newTeamId = $this->idGenerator->nextForClass(Team::class);

        if ($captainId && $this->RecordsBook->findPlayer($captainId) === null) {
            throw new UseCaseException(
                code: UseCaseException::$ENTITY_NOT_FOUND,
                message: 'Captain ID does not exist'
            );
        }

        $newTeam = new Team($newTeamId, $name, $captainId);
        $this->RecordsBook->registerTeam($newTeam);

        return $newTeamId;
    }
}