<?php namespace App\Soccer\Application\Players\AddProfilePicture;

use App\Soccer\Domain\Player\ProfPicRegistry;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\File\Image;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class AddProfilePicture extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook,
        private ProfPicRegistry $profPicRegistry
    ) { }

    public function execute(
        string $playerId,
        Image $profilePicture
    ): true {
        static::preventEmptyStringParams($playerId);

        if (!$this->recordsBook->findPlayer($playerId)) {
            throw new UseCaseException(
                "The player is not registered. Check ID.",
                UseCaseException::$ENTITY_NOT_FOUND
            );
        }

        $this->profPicRegistry->registerProfPic($playerId, $profilePicture);

        return true;
    }
}