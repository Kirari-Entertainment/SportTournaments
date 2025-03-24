<?php namespace App\Soccer\Application\Teams\List;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class TeamsList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if ($value instanceof TeamListEntry) {
            parent::offsetSet($offset, $value);
        } else {
            throw new \InvalidArgumentException('Value must be a TeamListEntry');
        }
    }

    public function offsetGet($offset): TeamListEntry {
        return parent::offsetGet($offset);
    }

    public function current(): TeamListEntry {
        return parent::current();
    }
}
