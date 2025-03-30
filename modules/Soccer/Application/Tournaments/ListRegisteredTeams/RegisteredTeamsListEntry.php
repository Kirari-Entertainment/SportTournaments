<?php namespace App\Soccer\Application\Tournaments\ListRegisteredTeams;

class RegisteredTeamsListEntry {
    public function __construct(
        public string $id,
        public string $name
    ) { }
}