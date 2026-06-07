<?php

declare(strict_types=1);

namespace App\Packages\Domain\ValueObject;

final class FileMetadata
{
    public function __construct(private readonly array $data) {}

    public function value(): array
    {
        return $this->data;
    }
}