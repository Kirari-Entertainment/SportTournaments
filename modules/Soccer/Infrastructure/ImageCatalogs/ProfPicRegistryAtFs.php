<?php namespace App\Soccer\Infrastructure\ImageCatalogs;

use App\Soccer\Domain\Player\ProfPicRegistry;
use Robust\Boilerplate\File\Image;
use Robust\Boilerplate\File\ImageCatalogAtFS;
use Robust\Boilerplate\File\ImageStandardizationConfig;

class ProfPicRegistryAtFs extends ImageCatalogAtFS implements ProfPicRegistry {
    public function __construct() {
        parent::__construct(
            __DIR__.'/player-profpics',
            new ImageStandardizationConfig(
                512,
                512,
                'jpg'
            )
        );
    }

    public function registerProfPic(string $playerId, Image $profPic): void {
        $this->addImage($playerId, $profPic);
    }

    public function retrieveProfPic(string $playerId): ?Image {
        return $this->retrieveImage($playerId);
    }

    public function deleteProfPic(string $playerId): void {
        $this->removeImage($playerId);
    }
}