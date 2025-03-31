<?php namespace Robust\Boilerplate\File;

class Image extends File {
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

    public function getBase64() : string {
        return base64_encode($this->getData());
    }
}