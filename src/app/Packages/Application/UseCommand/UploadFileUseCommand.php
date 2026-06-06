<?php

declare(strict_types=1);

namespace App\Packages\Application\UseCommand;

final class UploadFileUseCommand
{
    public function __construct(
        public readonly string $fileName,
        public readonly int    $fileSize,
        public readonly string $mimeType,
        public readonly string $filePath,
        public readonly string $storageDriver,
        public readonly string $contents,
        public readonly array  $metadata = [],
    ) {}
}
