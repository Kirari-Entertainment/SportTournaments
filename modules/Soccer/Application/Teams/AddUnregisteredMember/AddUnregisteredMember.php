<?php namespace App\Soccer\Application\Teams\AddUnregisteredMember;

use App\Soccer\Application\Players\Register\RegisterPlayer as RegisterManager;
use App\Soccer\Application\Teams\AddExistingMember\AddExistingMember as AddExistingMemberManager;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\IdGenerator;

class AddUnregisteredMember {
    private AddExistingMemberManager $addExistingMemberManager;

    public function __construct(
        private IdGenerator $idGenerator,
        private RecordsBook $recordsBook
    ) {
        $this->addExistingMemberManager = new AddExistingMemberManager($this->idGenerator, $this->recordsBook);
    }

    public function execute(
        string $tournamentId,
        string $teamId,
        string $playerName,
        string $playerLastName
    ) : string {
        $newlyRegisteredPlayerId = (new RegisterManager($this->idGenerator, $this->recordsBook))
            ->execute($playerName, $playerLastName);

        return $this->addExistingMemberManager->execute($tournamentId, $teamId, $newlyRegisteredPlayerId);
    }
}