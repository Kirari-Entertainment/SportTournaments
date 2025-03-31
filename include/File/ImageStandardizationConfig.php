<?php namespace Robust\Boilerplate\File;

readonly class ImageStandardizationConfig {
    public function __construct(
        public readonly int $maxWidth = 1200,
        public readonly int $maxHeight = 800,
        public readonly string $outputFormat = 'jpg',
        public readonly int $jpegQuality = 85,
        public readonly bool $enabled = true
    ) {}
}