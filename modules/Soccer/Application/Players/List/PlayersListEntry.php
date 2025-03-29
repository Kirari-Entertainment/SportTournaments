<?php namespace App\Soccer\Application\Players\List;

class PlayersListEntry {
    public function __construct(
        public string $id,
        public string $fullName,
    ) { }
}