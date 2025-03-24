<?php namespace Robust\Boilerplate\File;

use Robust\Boilerplate\Infrastructure\InfrastructureException;

class ImageCatalogAtFS {
    private string $directory;

    public function __construct(string $directory) {
        $this->directory = $directory;

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    public function addImage(string $id, Image $image) : void {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = explode('/', $image->getMime(), 2)[1];

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            throw new InfrastructureException(
                message: 'Invalid image file format.',
                code: InfrastructureException::$UNAVAILABLE
            );
        }

        $targetPath = $this->directory . '/' . $id . '.' . $extension;
        if (!file_put_contents($targetPath, $image->getData())) {
            throw new InfrastructureException(
                message: 'Failed to save the image file.',
                code: InfrastructureException::$INTERNAL_ERROR
            );
        }
    }

    public function retrieveImage(int|string $id) : ?Image {
        $files = glob($this->directory . '/' . $id . '.*');

        if (!$files) return null;
        
        return Image::fromFS($files[0]);
    }

    public function removeImage(int|string $id) : void {
        $files = glob($this->directory . '/' . $id . '.*');

        foreach ($files as $file) {
            unlink($file);
        }
    }
}   