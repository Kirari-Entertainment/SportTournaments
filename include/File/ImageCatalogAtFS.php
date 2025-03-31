<?php namespace Robust\Boilerplate\File;

use Robust\Boilerplate\Infrastructure\InfrastructureException;

class ImageCatalogAtFS {
    private string $directory;
    private ?ImageStandardizationConfig $standardizationConfig;

    public function __construct(
        string $directory,
        ?ImageStandardizationConfig $standardizationConfig = null
    ) {
        $this->directory = $directory;
        $this->standardizationConfig = $standardizationConfig;

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

        // Apply standardization if configured
        if ($this->standardizationConfig?->enabled) {
            $image = $this->standardizeImage($image);
            $extension = $this->standardizationConfig->outputFormat;
        }

        $targetPath = $this->directory . '/' . $id . '.' . $extension;
        if (!file_put_contents($targetPath, $image->getData())) {
            throw new InfrastructureException(
                message: 'Failed to save the image file.',
                code: InfrastructureException::$INTERNAL_ERROR
            );
        }
    }

    private function standardizeImage(Image $image): Image {
        // Create image resource from binary data
        $srcResource = imagecreatefromstring($image->getData());
        if ($srcResource === false) {
            throw new InfrastructureException(
                message: 'Invalid image data.',
                code: InfrastructureException::$UNAVAILABLE
            );
        }

        // Get original dimensions
        $srcWidth = imagesx($srcResource);
        $srcHeight = imagesy($srcResource);

        // Calculate target dimensions while maintaining aspect ratio
        $ratio = min(
            $this->standardizationConfig->maxWidth / $srcWidth,
            $this->standardizationConfig->maxHeight / $srcHeight
        );
        $targetWidth = (int)($srcWidth * $ratio);
        $targetHeight = (int)($srcHeight * $ratio);

        // Create new canvas
        $targetResource = imagecreatetruecolor($targetWidth, $targetHeight);

        // Enable alpha blending for PNG transparency if needed
        if ($image->getMime() === 'image/png') {
            imagealphablending($targetResource, false);
            imagesavealpha($targetResource, true);
        }

        // Resize the image
        imagecopyresampled(
            $targetResource, $srcResource,
            0, 0, 0, 0,
            $targetWidth, $targetHeight, $srcWidth, $srcHeight
        );

        // Start output buffering to capture the processed image data
        ob_start();

        // Output the processed image
        $format = $this->standardizationConfig->outputFormat;
        switch ($format) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($targetResource, null, $this->standardizationConfig->jpegQuality);
                $mime = 'image/jpeg';
                break;
            case 'png':
                imagepng($targetResource);
                $mime = 'image/png';
                break;
            case 'gif':
                imagegif($targetResource);
                $mime = 'image/gif';
                break;
            default:
                imagejpeg($targetResource, null, $this->standardizationConfig->jpegQuality);
                $mime = 'image/jpeg';
        }

        // Get the processed image data
        $processedData = ob_get_clean();

        // Free resources
        imagedestroy($srcResource);
        imagedestroy($targetResource);

        // Create new image object with processed data
        return new Image(
            data: $processedData,
            mime: $mime
        );
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