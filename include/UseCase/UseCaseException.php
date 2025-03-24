<?php namespace Robust\Boilerplate\UseCase;

class UseCaseException extends \Exception {
    public static int $INVALID_PARAMETER = 1;
    public static int $ENTITY_NOT_FOUND = 2;
    public static int $ENTITY_ALREADY_EXISTS = 3;
    public static int $NOT_ALLOWED = 4;
}