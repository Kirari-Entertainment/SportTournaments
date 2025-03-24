<?php namespace App\Soccer\Application\Teams\AddMember;

use App\Soccer\Domain\Player\TeamMembership;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\UseCaseException;

class Manager {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $tournamentId,
        string $teamId,
        string $playerId,
    ) : void {
        $tournament = $this->recordsBook->findTournament($tournamentId);
        $team = $this->recordsBook->findTeam($teamId);
        $player = $this->recordsBook->findPlayer($playerId);

        $this->preventEmptyParameters($tournamentId, $teamId, $playerId);
        $this->checkIfInscribable($tournament);

        $membershipInTournament = new TeamMembership(
            $this->idGenerator->nextForClass(TeamMembership::class),
            $tournament,
            $player,
            $team
        );

        $this->recordsBook->registerTeamMembership($membershipInTournament);

    }

    private function preventEmptyParameters(string ...$params) {
        if (in_array('', $params)) {
            throw new UseCaseException(
                'Empty parameters',
                UseCaseException::$INVALID_PARAMETER
            );
        }
    }

    private function checkIfInscribable($tournament) {
        if (!$tournament->isInscribable()) {
            throw new UseCaseException(
                'Tournament is not inscribable',
                UseCaseException::$INVALID_PARAMETER
            );
        }
    }
}