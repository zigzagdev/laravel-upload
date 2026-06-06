<?php

declare(strict_types=1);

namespace App\Packages\Infrastructure;

use App\Models\UploadFileMongoModel;
use App\Packages\Domain\Repository\MongoFileRepositoryInterface;
use App\Packages\Domain\ValueObject\FileId;
use App\Packages\Domain\ValueObject\FileMetadata;

// Stores flexible, file-type-specific metadata (e.g. EXIF for images, duration for videos) in MongoDB, linked by file_id.
class MongoFileRepository implements MongoFileRepositoryInterface
{
    public function __construct(
        private readonly UploadFileMongoModel $fileModel,
    ) {}

    public function save(FileId $fileId, FileMetadata $metadata): void
    {
        $this->fileModel->newInstance([
            'file_id'  => $fileId->value(),
            'metadata' => $metadata->value(),
        ])->save();
    }
}