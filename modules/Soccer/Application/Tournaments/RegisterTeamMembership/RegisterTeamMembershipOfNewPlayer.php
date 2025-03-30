<?php

namespace App\Soccer\Application\Tournaments\RegisterTeamMembership;

use App\Soccer\Application\Players\Register\RegisterPlayer;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

readonly class RegisterTeamMembershipOfNewPlayer extends InteractorWithUtils {
    private RegisterTeamMembership $registerTeamMembership;
    private RegisterPlayer $registerPlayer;
    public function __construct(
        IdGenerator $idGenerator,
        RecordsBook $recordsBook
    ) {
        $this->registerTeamMembership = new RegisterTeamMembership($recordsBook);
        $this->registerPlayer = new RegisterPlayer($idGenerator, $recordsBook);
    }

    public function execute(
        string $tournamentId,
        string $teamId,
        string $playerName,
        string $playerLastName
    ): true {
        $playerId = $this->registerPlayer->execute($playerName, $playerLastName);
        $this->registerTeamMembership->execute($tournamentId, $teamId, $playerId);
        return true;
    }
}