<?php namespace App\Soccer\Application\Teams\List;

class TeamListEntry {
    public function __construct(
        public string $id,
        public string $name
    ) { }
}