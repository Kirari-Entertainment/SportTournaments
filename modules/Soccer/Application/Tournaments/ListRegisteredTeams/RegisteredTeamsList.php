<?php namespace App\Soccer\Application\Tournaments\ListRegisteredTeams;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class RegisteredTeamsList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if (!$value instanceof RegisteredTeamsListEntry) {
            throw new \InvalidArgumentException('Value must be a RegisteredTeamsListEntry');
        }
        parent::offsetSet($offset, $value);
    }
}