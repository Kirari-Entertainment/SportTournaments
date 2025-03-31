<?php namespace App\Soccer\Domain\Player;

use Robust\Boilerplate\File\Image;

interface ProfPicRegistry {
    public function registerProfPic(string $playerId, Image $profPic): void;
    public function retrieveProfPic(string $playerId): ?Image;
    public function deleteProfPic(string $playerId): void;
}