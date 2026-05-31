<?php

declare(strict_types=1);

use App\Packages\Domain\File\Exception\InvalidFileSizeException;
use App\Packages\Domain\File\ValueObject\FileSize;

describe('FileSize', function () {
    describe('constructor', function () {
        it('accepts 1 byte as the minimum', function () {
            $size = new FileSize(1);

            expect($size->value())->toBe(1);
        });

        it('accepts the maximum size (100MB)', function () {
            $size = new FileSize(FileSize::MAX_SIZE_BYTES);

            expect($size->value())->toBe(FileSize::MAX_SIZE_BYTES);
        });

        it('throws when size is 0', function () {
            new FileSize(0);
        })->throws(InvalidFileSizeException::class, 'at least 1 byte');

        it('throws when size is negative', function () {
            new FileSize(-1);
        })->throws(InvalidFileSizeException::class, 'at least 1 byte');

        it('throws when size exceeds 100MB', function () {
            new FileSize(FileSize::MAX_SIZE_BYTES + 1);
        })->throws(InvalidFileSizeException::class, '100MB');
    });

    describe('equals()', function () {
        it('returns true for the same size', function () {
            $a = new FileSize(1024);
            $b = new FileSize(1024);

            expect($a->equals($b))->toBeTrue();
        });

        it('returns false for different sizes', function () {
            $a = new FileSize(1024);
            $b = new FileSize(2048);

            expect($a->equals($b))->toBeFalse();
        });
    });
});