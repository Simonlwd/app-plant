<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload(UploadedFile $file, string $targetDir): string
    {
        $safeName = bin2hex(random_bytes(12));
        $extension = $file->guessExtension();

        if (!$extension) {
            $extension = 'bin';
        }

        $newFilename = $safeName . '.' . $extension;

        $file->move($targetDir, $newFilename);

        return $newFilename;
    }

    /**
     * Verwijder een bestand als het bestaat
     */
    public function deleteFile(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }
}
