<?php namespace App\Soccer\Application\Tournaments\ListTeamMemberships;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class TeamMembersList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if (!$value instanceof TeamMembersListEntry) {
            throw new \InvalidArgumentException('Value must be a TeamMembersListEntry');
        }
        parent::offsetSet($offset, $value);
    }
}