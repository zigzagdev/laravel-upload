<?php

declare(strict_types=1);

namespace App\Packages\Infra\File\Storage;

use App\Packages\Domain\File\ValueObject\FilePath;
use App\Packages\Domain\File\ValueObject\MimeType;
use DateTimeImmutable;

interface FileStorageInterface
{
    public function upload(FilePath $path, mixed $contents, MimeType $mimeType): void;

    public function delete(FilePath $path): void;

    public function temporaryUrl(FilePath $path, DateTimeImmutable $expiration): string;
}