<?php namespace Robust\Boilerplate\Infrastructure;

class InfrastructureException extends \Exception {
    public static int $UNAVAILABLE = 1;
    public static int $INTERNAL_ERROR = 2;
    public static int $EXCEDEED_QUOTA = 3;
}