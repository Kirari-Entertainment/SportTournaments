<?php namespace App\Soccer\Application\Tournaments\ListScheduledGames;

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class GamesList extends TypedArrayDTO {
    public function offsetSet($offset, $value): void {
        if (!$value instanceof GamesListEntry) {
            throw new \InvalidArgumentException('Value must be a GamesListEntry instance');
        }
        parent::offsetSet($offset, $value);
    }
}