<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class EventImageUploader
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private readonly string $eventImagesDirectory)
    {
    }

    public function upload(UploadedFile $file): string
    {
        if (!$file->isValid() || $file->getSize() === false || $file->getSize() > self::MAX_FILE_SIZE) {
            throw new FileException('L’image est invalide ou dépasse la taille maximale de 2 Mo.');
        }

        $mimeType = $file->getMimeType();
        $extension = self::ALLOWED_MIME_TYPES[$mimeType] ?? null;

        if ($extension === null) {
            throw new FileException("Le type d'image n'est pas autorisé.");
        }

        if (!is_dir($this->eventImagesDirectory)
            && !mkdir($concurrentDirectory = $this->eventImagesDirectory, 0775, true)
            && !is_dir($concurrentDirectory)) {
            throw new FileException("Le dossier d'upload n'est pas disponible.");
        }

        $fileName = bin2hex(random_bytes(16)).'.'.$extension;
        $file->move($this->eventImagesDirectory, $fileName);

        return 'events/'.$fileName;
    }

    public function remove(?string $relativePath): void
    {
        if ($relativePath === null || !str_starts_with(str_replace('\\', '/', $relativePath), 'events/')) {
            return;
        }

        $filePath = $this->eventImagesDirectory.DIRECTORY_SEPARATOR.basename($relativePath);
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
}
