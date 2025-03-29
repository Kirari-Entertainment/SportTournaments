<?php namespace App\Soccer\Application\Players\List;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class PlayersList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if (!$value instanceof PlayersListEntry) {
            throw new \InvalidArgumentException('Value must be a PlayersListEntry');
        }
        parent::offsetSet($offset, $value);
    }
}