<?php namespace Robust\Boilerplate\File;

class Image {
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

    public static function fromFS(string $path) : self {
        $data = file_get_contents($path);
        $mime = mime_content_type($path);

        return new self($data, $mime);
    }

    public static function fromBase64(string $base64) : self {
        $data = base64_decode($base64);
        $mime = mime_content_type($base64);

        return new self($data, $mime);
    }
}