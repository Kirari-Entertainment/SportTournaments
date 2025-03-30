<?php namespace App\Soccer\Application\Teams\Register;

use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\UseCaseException;

class RegisterTeam {
    public function __construct(
        private readonly IdGenerator $idGenerator,
        private readonly RecordsBook $RecordsBook
    ) { }

    public function execute(string $name, ?string $captainId = null) : string {
        $newTeamId = $this->idGenerator->nextForClass(Team::class);

        if ($captainId && $this->RecordsBook->findPlayer($captainId) === null) {
            throw new UseCaseException(
                message: 'Captain ID does not exist',
                code: UseCaseException::$ENTITY_NOT_FOUND
            );
        }

        $newTeam = new Team($newTeamId, $name, $captainId);
        $this->RecordsBook->registerTeam($newTeam);

        return $newTeamId;
    }
}