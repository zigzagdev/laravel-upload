<?php

declare(strict_types=1);

namespace App\Packages\Domain\File\Entity;

use App\Packages\Domain\File\ValueObject\FileId;
use App\Packages\Domain\File\ValueObject\FileName;
use App\Packages\Domain\File\ValueObject\FileSize;
use App\Packages\Domain\File\ValueObject\FilePath;
use App\Packages\Domain\File\ValueObject\MimeType;
use App\Packages\Domain\File\ValueObject\StorageDriver;

final class UploadFile
{
    private function __construct(
        private readonly FileId $fileId,
        private readonly FileName $fileName,
        private readonly FileSize $fileSize,
        private readonly MimeType $mimeType,
        private readonly FilePath $filePath,
        private readonly StorageDriver $storageDriver,
    ) {}

    public static function create(
        FileName $fileName,
        FileSize $fileSize,
        MimeType $mimeType,
        FilePath $filePath,
        StorageDriver $storageDriver,
    ): self {
        return new self(
            FileId::generate(),
            $fileName,
            $fileSize,
            $mimeType,
            $filePath,
            $storageDriver,
        );
    }

    public static function reconstitute(
        FileId $fileId,
        FileName $fileName,
        FileSize $fileSize,
        MimeType $mimeType,
        FilePath $filePath,
        StorageDriver $storageDriver,
    ): self {
        return new self($fileId, $fileName, $fileSize, $mimeType, $filePath, $storageDriver);
    }

    public function fileId(): FileId
    {
        return $this->fileId;
    }

    public function fileName(): FileName
    {
        return $this->fileName;
    }

    public function fileSize(): FileSize
    {
        return $this->fileSize;
    }

    public function mimeType(): MimeType
    {
        return $this->mimeType;
    }

    public function filePath(): FilePath
    {
        return $this->filePath;
    }

    public function storageDriver(): StorageDriver
    {
        return $this->storageDriver;
    }
}