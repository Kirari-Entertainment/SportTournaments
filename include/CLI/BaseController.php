<?php namespace Robust\Boilerplate\CLI;

abstract class BaseController {
    public static function parseOptions() : array {
        global $argv;
        
        $options = [];
        foreach ($argv as $arg) {
            if (strpos($arg, '--') === 0) {
                $arg = substr($arg, 2);
                $arg = explode('=', $arg);
                $options[$arg[0]] = $arg[1] ?? true;
            }
        }

        return $options;
    }

    public static function parsePositionalParams() : array {
        global $argv;
        
        $params = $argv;

        return $params;
    }

    public abstract static function unknown() : void;
}