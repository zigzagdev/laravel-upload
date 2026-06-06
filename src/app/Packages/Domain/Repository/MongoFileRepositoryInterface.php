<?php

declare(strict_types=1);

namespace App\Packages\Domain\Repository;

use App\Packages\Domain\ValueObject\FileId;
use App\Packages\Domain\ValueObject\FileMetadata;

interface MongoFileRepositoryInterface
{
    public function save(FileId $fileId, FileMetadata $metadata): void;
}