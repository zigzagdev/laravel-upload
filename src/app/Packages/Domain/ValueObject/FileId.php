<?php

declare(strict_types=1);

namespace App\Packages\Domain\ValueObject;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class FileId
{
    public function __construct(private readonly string $value)
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            throw new InvalidArgumentException("Invalid FileId format: {$value}");
        }
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
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