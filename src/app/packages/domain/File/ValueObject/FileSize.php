<?php

declare(strict_types=1);

namespace App\Packages\Domain\File\ValueObject;

use App\Packages\Domain\File\Exception\InvalidFileSizeException;

final class FileSize
{
    public const MAX_SIZE_BYTES = 104_857_600; // 100MB

    public function __construct(private readonly int $value)
    {
        if ($value < 1) {
            throw new InvalidFileSizeException('File size must be at least 1 byte.');
        }

        if ($value > self::MAX_SIZE_BYTES) {
            throw new InvalidFileSizeException(
                sprintf('File size must not exceed %d bytes (100MB).', self::MAX_SIZE_BYTES)
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}