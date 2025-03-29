<?php namespace App\Soccer\Application\Players\Register;

use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;

class Manager {
    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {}

    public function execute(
        string $name,
        string $lastName
    ) : string {
        $newPlayerId = $this->idGenerator->nextForClass(Player::class);

        $newPlayer = new Player(
            $newPlayerId,
            $name,
            $lastName
        );

        $this->recordsBook->registerPlayer($newPlayer);

        return $newPlayerId;
    }
}