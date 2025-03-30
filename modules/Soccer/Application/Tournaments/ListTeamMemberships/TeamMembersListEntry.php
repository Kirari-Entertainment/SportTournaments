<?php namespace App\Soccer\Application\Tournaments\ListTeamMemberships;

readonly class TeamMembersListEntry {
    public function __construct(
        public string $playerId,
        public string $playerName
    ) { }
}