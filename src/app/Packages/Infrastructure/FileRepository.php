<?php

declare(strict_types=1);

namespace App\Packages\Infrastructure;

use App\Models\UploadFileModel;
use App\Packages\Domain\Entity\UploadFile;
use App\Packages\Domain\Repository\FileRepositoryInterface;

// Stores fixed, structured metadata (file_id, name, size, mime_type, path, driver) in MySQL for search and aggregation.
class FileRepository implements FileRepositoryInterface
{
    public function __construct(
        private readonly UploadFileModel $fileModel,
    ) {}

    public function save(UploadFile $file): void
    {
        $this->fileModel->newInstance([
            'file_id'        => $file->fileId()->value(),
            'file_name'      => $file->fileName()->value(),
            'file_size'      => $file->fileSize()->value(),
            'mime_type'      => $file->mimeType()->value(),
            'file_path'      => $file->filePath()->value(),
            'storage_driver' => $file->storageDriver()->value,
        ])->save();
    }
}