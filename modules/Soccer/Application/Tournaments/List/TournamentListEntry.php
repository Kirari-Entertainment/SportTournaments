<?php namespace App\Soccer\Application\Tournaments\List;

class TournamentListEntry {
    public function __construct(
        public string $id,
        public string $name,
    ) { }
}