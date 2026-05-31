<?php

declare(strict_types=1);

namespace App\Packages\Domain\Interface;

use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;

interface FileInterface
{
    public function upload(FilePath $path, mixed $contents, MimeType $mimeType): void;
}