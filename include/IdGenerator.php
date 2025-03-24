<?php namespace Robust\Boilerplate;

/**
 * El generador de identificadores se puede reemplazar siempre y cuando
 * implemente esta interfaz.
 */
interface IdGenerator {
    public function nextUniversal() : int|string;
    public function nextForClass(string $className) : int|string;
    public function getNForClass(int $n, string $className) : array;
}