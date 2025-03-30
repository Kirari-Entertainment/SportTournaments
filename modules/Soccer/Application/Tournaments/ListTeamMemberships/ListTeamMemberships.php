<?php namespace App\Soccer\Application\Tournaments\ListTeamMemberships;

use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class ListTeamMemberships extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(
        string $tournamentId,
        string $teamId
    ): TeamMembersList {
        $tournament = $this->recordsBook->findTournament($tournamentId);
        if ($tournament === null) {
            throw new UseCaseException(
                'Tournament not found',
                UseCaseException::$ENTITY_NOT_FOUND
            );
        }

        $teamMembershipsOfTournament = $this->recordsBook->retrieveTeamMembershipsByTournament($tournamentId);
        $teamMembershipsList = new TeamMembersList();

        foreach ($teamMembershipsOfTournament as $teamMembership) {
            if ($teamMembership->getTeam()->getId() === $teamId) {
                $player = $this->recordsBook->findPlayer($teamMembership->getPlayer()->getId());
                if ($player === null) {
                    throw new UseCaseException(
                        'Player not found',
                        UseCaseException::$ENTITY_NOT_FOUND
                    );
                }
                $teamMembershipsList[] = new TeamMembersListEntry(
                    $player->getId(),
                    $player->getFullName()
                );
            }
        }

        return $teamMembershipsList;
    }
}