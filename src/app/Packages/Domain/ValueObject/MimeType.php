<?php

declare(strict_types=1);

namespace App\Packages\Domain\ValueObject;

use App\Packages\Domain\Exception\InvalidMimeTypeException;

final class MimeType
{
    private const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'video/mp4',
        'audio/mpeg',
        'text/plain',
        'text/csv',
        'application/zip',
    ];

    public function __construct(private readonly string $value)
    {
        if (!in_array($value, self::ALLOWED_TYPES, true)) {
            throw new InvalidMimeTypeException(
                sprintf("MIME type '%s' is not allowed.", $value)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    /** @return string[] */
    public static function allowedTypes(): array
    {
        return self::ALLOWED_TYPES;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}