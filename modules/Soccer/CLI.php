<?php namespace Kirari\Series;

use Kirari\Series\Infrastructure\CLIControllers\SeriesController;
use Kirari\Series\Infrastructure\CLIControllers\AuthorController;
use Kirari\Series\Infrastructure\CLIControllers\BookController;
use Kirari\Series\Infrastructure\CLIControllers\ChapterController;
use Kirari\Series\Infrastructure\CLIControllers\PublisherController;

class CLI {
    public static function listen() : void {
        global $module;
        global $command;

        match ($module) {
            'series' => match ($command) {
                'add' => SeriesController::register(),
                'ls' => SeriesController::list(),
                'show' => SeriesController::show(),
                'update' => SeriesController::update(),
                'rm' => SeriesController::delete(),
                'add-genre' => SeriesController::addGenre(),
                'migrate' => SeriesController::migrate(),
                default => SeriesController::unknown(),
            },
            
            'author' => match ($command) {
                'add' => AuthorController::register(),
                'ls' => AuthorController::list(),
                default => AuthorController::unknown(),
            },

            'publisher' => match ($command) {
                'add' => PublisherController::register(),
                'ls' => PublisherController::list(),
                default => PublisherController::unknown(),
            },

            'book' => match ($command) {
                'add' => BookController::register(),
                'ls' => BookController::list(),
                'show' => BookController::show(),
                'update' => BookController::update(),
                'rm' => BookController::delete(),
                'add-collaborator' => BookController::addCollaborator(),
                'test' => static::test(),
                default => BookController::unknown(),
            },

            'chapter' => match ($command) {
                'add' => ChapterController::register(),
                'ls' => ChapterController::list(),
                'show' => ChapterController::show(),
                'update' => ChapterController::update(),
                default => ChapterController::unknown(),
            },

            default => null,
        };
    }

    private static function test() : void {
        global $moduleMatched;
        $moduleMatched = true;
        
        echo "Test command\n";
    }
}