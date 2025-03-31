<?php namespace App\Soccer\Application\Players\GetProfilePicture;

use App\Soccer\Domain\Player\ProfPicRegistry;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\File\Image;
use Robust\Boilerplate\UseCase\InteractorWithUtils;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class GetProfilePicture extends InteractorWithUtils {
    public function __construct(
        private RecordsBook $recordsBook,
        private ProfPicRegistry $profPicRegistry
    ) { }

    public function execute(
        string $playerId
    ) : ?Image {
        static::preventEmptyStringParams($playerId);
        if (!$this->recordsBook->findPlayer($playerId)) {
            throw new UseCaseException(
                "Unknown player",
                UseCaseException::$ENTITY_NOT_FOUND
            );
        }
        return $this->profPicRegistry->retrieveProfPic($playerId);
    }
}