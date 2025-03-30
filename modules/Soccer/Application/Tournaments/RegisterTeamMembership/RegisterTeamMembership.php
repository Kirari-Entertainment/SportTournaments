<?php

namespace App\Soccer\Application\Tournaments\RegisterTeamMembership;

use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\TeamMembership;
use App\Soccer\Domain\Tournament\Tournament;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class RegisterTeamMembership extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) { }

    public function execute(
        string $tournamentId,
        string $teamId,
        string $playerId
    ): true {
        $tournament = $this->recordsBook->findTournament($tournamentId);
        if ($tournament === null) {
            throw new UseCaseException(
                'Tournament not found',
                UseCaseException::$ENTITY_NOT_FOUND
            );
        }

        $team = $this->getRegisteredTeamOrThrowException($tournament, $teamId);

        $player = $this->recordsBook->findPlayer($playerId);
        if ($player === null) {
            throw new UseCaseException(
                'Player not found',
                UseCaseException::$ENTITY_NOT_FOUND
            );
        }
        if ($this->isPlayerAlreadyRegisteredInTournament($playerId, $tournament)) {
            throw new UseCaseException(
                'Player already registered in tournament',
                UseCaseException::$INVALID_PARAMETER
            );
        }

        $teamMembership = new TeamMembership(
            $tournament,
            $team,
            $player
        );

        $this->recordsBook->registerTeamMembership($teamMembership);
        return true;
    }

    private function getRegisteredTeamOrThrowException(
        Tournament $tournament,
        string $teamId
    ): Team {
        $team = current(array_filter(
            $tournament->getRegisteredTeams(),
            fn($team) => $team->getId() === $teamId
        ));

        if ($team === null) {
            throw new UseCaseException(
                'Team not registered in tournament',
                UseCaseException::$INVALID_PARAMETER
            );
        }

        return $team;
    }

    private function isPlayerAlreadyRegisteredInTournament(
        string $playerId,
        Tournament $tournament
    ): bool {
        $tournamentMemberships = $this->recordsBook->retrieveTeamMembershipsByTournament($tournament->getId());

        return in_array(
            $playerId,
            array_map(
                fn($teamMembership) => $teamMembership->getPlayer()->getId(),
                $tournamentMemberships
            )
        );
    }
}