<?php namespace App\Soccer\Application\Teams\AddExistingMember;

use App\Soccer\Domain\Player\TeamMembership;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

class Manager extends InteractorWithUtils {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $tournamentId,
        string $teamId,
        string $playerId,
    ) : string {
        static::preventEmptyStringParams($tournamentId, $teamId, $playerId);
        
        $tournament = $this->recordsBook->findTournament($tournamentId);
        $team = $this->recordsBook->findTeam($teamId);
        $player = $this->recordsBook->findPlayer($playerId);
        
        $this->checkIfInscribable($tournament);
        $teamMembershipId = $this->idGenerator->nextForClass(TeamMembership::class);

        $membershipInTournament = new TeamMembership(
            $teamMembershipId,
            $tournament,
            $player,
            $team
        );

        $this->recordsBook->registerTeamMembership($membershipInTournament);

        return $teamMembershipId;
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