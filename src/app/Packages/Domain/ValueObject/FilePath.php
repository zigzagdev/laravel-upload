<?php

declare(strict_types=1);

namespace App\Packages\Domain\ValueObject;

use InvalidArgumentException;

final class FilePath
{
    public function __construct(private readonly string $value)
    {
        if ($value === '') {
            throw new InvalidArgumentException('File path cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}