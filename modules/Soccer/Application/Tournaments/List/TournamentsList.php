<?php namespace App\Soccer\Application\Tournaments\List;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class TournamentsList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if (!$value instanceof TournamentListEntry) {
            throw new \InvalidArgumentException('Value must be a TournamentListEntry');
        }

        parent::offsetSet($offset, $value);
    }
}