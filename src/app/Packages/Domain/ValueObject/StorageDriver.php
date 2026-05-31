<?php

declare(strict_types=1);

namespace App\Packages\Domain\File\ValueObject;

enum StorageDriver: string
{
    case S3 = 's3';
    case GCS = 'gcs';
}