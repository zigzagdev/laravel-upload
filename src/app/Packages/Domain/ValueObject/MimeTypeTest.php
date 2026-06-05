<?php

declare(strict_types=1);

use App\Packages\Domain\Exception\InvalidMimeTypeException;
use App\Packages\Domain\ValueObject\MimeType;

describe('MimeType', function () {
    describe('constructor', function () {
        it('accepts each allowed MIME type', function (string $type) {
            $mimeType = new MimeType($type);

            expect($mimeType->value())->toBe($type);
        })->with(MimeType::allowedTypes());

        it('throws when given a disallowed type', function () {
            new MimeType('application/x-executable');
        })->throws(InvalidMimeTypeException::class, 'not allowed');

        it('throws when given an empty string', function () {
            new MimeType('');
        })->throws(InvalidMimeTypeException::class, 'not allowed');
    });

    describe('allowedTypes()', function () {
        it('returns a non-empty list', function () {
            expect(MimeType::allowedTypes())->not->toBeEmpty();
        });

        it('contains expected types', function () {
            expect(MimeType::allowedTypes())
                ->toContain('image/jpeg')
                ->toContain('application/pdf')
                ->toContain('video/mp4');
        });
    });

    describe('equals()', function () {
        it('returns true for the same type', function () {
            $a = new MimeType('image/png');
            $b = new MimeType('image/png');

            expect($a->equals($b))->toBeTrue();
        });

        it('returns false for different types', function () {
            $a = new MimeType('image/png');
            $b = new MimeType('image/jpeg');

            expect($a->equals($b))->toBeFalse();
        });
    });
});
