<?php namespace Robust\Boilerplate\File;

abstract class File {
    public function __construct(
        private string $data,
        private string $mime
    ) {}

    public function getData() : string {
        return $this->data;
    }

    public function getMime() : string {
        return $this->mime;
    }
}