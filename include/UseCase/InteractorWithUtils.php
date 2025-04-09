<?php namespace Robust\Boilerplate\UseCase;

readonly abstract class InteractorWithUtils {
    protected static function preventEmptyStringParams(string ...$params) {
        if (array_filter($params, fn($param) => trim($param) === '')) {
            throw new UseCaseException(
            'Empty parameters: ' . implode(', ', array_keys(array_filter($params, fn($param) => trim($param) === ''))),
            UseCaseException::$INVALID_PARAMETER
            );
        }
    }

    protected static function throwEntityNotFound(string $entityName) {
        throw new UseCaseException(
            "$entityName not found",
            UseCaseException::$ENTITY_NOT_FOUND
        );
    }
}