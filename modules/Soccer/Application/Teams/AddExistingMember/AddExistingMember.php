<?php namespace App\Soccer\Application\Teams\AddExistingMember;

use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Tournament\TeamMembership;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

class AddExistingMember extends InteractorWithUtils {
    public function __construct(
        private readonly IdGenerator $idGenerator,
        private readonly RecordsBook $recordsBook
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
        
        $this->ensureInscribable($tournament);
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

    private function ensureInscribable($tournament) : void {
        if (!$tournament->isInscribable()) {
            throw new UseCaseException(
                'Tournament is not inscribable',
                UseCaseException::$INVALID_PARAMETER
            );
        }
    }
}