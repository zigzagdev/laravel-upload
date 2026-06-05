<?php

declare(strict_types=1);

namespace App\Packages\Domain\Repository;

use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;

interface CloudStorageInterface
{
    public function upload(FilePath $path, mixed $contents, MimeType $mimeType): void;
}