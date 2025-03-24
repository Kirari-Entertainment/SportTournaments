<?php namespace App\Soccer\Application\Tournaments\Register;

use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Tournament\Tournament;
use DateTime;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

class Manager extends InteractorWithUtils {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $name,
        string $description,
        string $startDate,
        string $endDate,
        string $inscriptionStartDate,
        string $inscriptionEndDate
    ) : mixed {
        $tournamentId = $this->idGenerator->nextForClass(Tournament::class);

        $this->preventEmptyStringParams(
            $name,
            $description,
            $startDate,
            $endDate,
            $inscriptionStartDate,
            $inscriptionEndDate
        );

        $tournament = new Tournament(
            $tournamentId,
            $name,
            $description,
            new DateTime($startDate),
            new DateTime($endDate),
            new DateTime($inscriptionStartDate),
            new DateTime($inscriptionEndDate)
        );

        $this->recordsBook->registerTournament($tournament);

        return $tournamentId;
    }
}