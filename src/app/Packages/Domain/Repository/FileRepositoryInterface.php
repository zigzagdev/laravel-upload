<?php

declare(strict_types=1);

namespace App\Packages\Domain\Repository;

use App\Packages\Domain\Entity\UploadFile;

interface FileRepositoryInterface
{
    public function save(UploadFile $file): void;
}