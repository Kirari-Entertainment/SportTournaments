<?php namespace App\Soccer\Domain\Game;

enum GameStatus : int {
    case SCHEDULED = 1;
    case IN_PROGRESS = 2;
    case FINISHED = 3;
    case CANCELLED = 4;
}