<?php namespace App\Soccer\Application\Players\List;

use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\UseCase\InteractorWithUtils;

class Manager extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook
    ) {}

    public function execute() : PlayersList {
        $allPlayers = $this->recordsBook->retrieveAllPlayers();
        $playersList = new PlayersList();

        foreach ($allPlayers as $player) {
            $playersList[] = new PlayersListEntry(
                $player->getId(),
                $player->getFullName()
            );
        }

        return $playersList;
    }
}