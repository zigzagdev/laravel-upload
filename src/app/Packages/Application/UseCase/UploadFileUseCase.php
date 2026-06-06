<?php

declare(strict_types=1);

namespace App\Packages\Application\UseCase;

use App\Packages\Application\UseCommand\UploadFileUseCommand;
use App\Packages\Domain\Entity\UploadFile;
use App\Packages\Domain\Repository\CloudStorageInterface;
use App\Packages\Domain\Repository\FileRepositoryInterface;
use App\Packages\Domain\Repository\MongoFileRepositoryInterface;
use App\Packages\Domain\ValueObject\FileMetadata;
use App\Packages\Domain\ValueObject\FileName;
use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\FileSize;
use App\Packages\Domain\ValueObject\MimeType;
use App\Packages\Domain\ValueObject\StorageDriver;

final class UploadFileUseCase
{
    public function __construct(
        private readonly CloudStorageInterface        $cloudStorage,
        private readonly FileRepositoryInterface      $fileRepository,
        private readonly MongoFileRepositoryInterface $mongoFileRepository,
    ) {}

    public function handle(UploadFileUseCommand $command): void
    {
        $file = UploadFile::create(
            new FileName($command->fileName),
            new FileSize($command->fileSize),
            new MimeType($command->mimeType),
            new FilePath($command->filePath),
            StorageDriver::from($command->storageDriver),
        );

        $this->cloudStorage->upload($file->filePath(), $command->contents, $file->mimeType());

        $this->fileRepository->save($file);

        $this->mongoFileRepository->save(
            $file->fileId(),
            new FileMetadata($command->metadata),
        );
    }
}
