<?php namespace App\Soccer\Domain\Tournament;

enum Status {
    case PLANNED;
    case INSCRIPTION;
    case ONGOING;
    case FINISHED;
}