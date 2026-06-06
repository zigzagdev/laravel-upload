<?php

declare(strict_types=1);

namespace App\Packages\Infrastructure\Adapter;

use App\Packages\Domain\Repository\CloudStorageInterface;
use App\Packages\Domain\ValueObject\StorageDriver;

class CloudStorageResolver
{
    public function __construct(
        private readonly S3Adapter $s3Adapter,
        private readonly GCSAdapter $gcsAdapter,
    ) {}

    public function resolve(StorageDriver $driver): CloudStorageInterface
    {
        return match ($driver) {
            StorageDriver::S3  => $this->s3Adapter,
            StorageDriver::GCS => $this->gcsAdapter,
        };
    }
}