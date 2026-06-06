<?php

declare(strict_types=1);

namespace App\Packages\Infrastructure\Adapter;

use App\Packages\Domain\Repository\CloudStorageInterface;
use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;
use League\Flysystem\Filesystem;

class S3Adapter implements CloudStorageInterface
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {}

    public function upload(FilePath $path, mixed $contents, MimeType $mimeType): void
    {
        $this->filesystem->write($path->value(), $contents, [
            'ContentType' => $mimeType->value(),
        ]);
    }
}