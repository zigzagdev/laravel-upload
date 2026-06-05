<?php

declare(strict_types=1);

namespace App\Packages\Domain\ValueObject;

use App\Packages\Domain\Exception\InvalidFileNameException;

final class FileName
{
    public const MAX_LENGTH = 255;

    public function __construct(private readonly string $value)
    {
        if ($value === '') {
            throw new InvalidFileNameException('File name cannot be empty.');
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidFileNameException(
                sprintf('File name must not exceed %d characters.', self::MAX_LENGTH)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function extension(): string
    {
        return pathinfo($this->value, PATHINFO_EXTENSION);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}