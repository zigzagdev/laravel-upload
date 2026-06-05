<?php

declare(strict_types=1);

use App\Packages\Domain\ValueObject\FilePath;

describe('FilePath', function () {
    describe('constructor', function () {
        it('accepts a valid path', function () {
            $path = new FilePath('uploads/2024/01/photo.jpeg');

            expect($path->value())->toBe('uploads/2024/01/photo.jpeg');
        });

        it('throws when given an empty string', function () {
            new FilePath('');
        })->throws(InvalidArgumentException::class, 'File path cannot be empty.');
    });

    describe('equals()', function () {
        it('returns true for the same path', function () {
            $a = new FilePath('uploads/file.txt');
            $b = new FilePath('uploads/file.txt');

            expect($a->equals($b))->toBeTrue();
        });

        it('returns false for different paths', function () {
            $a = new FilePath('uploads/file.txt');
            $b = new FilePath('uploads/other.txt');

            expect($a->equals($b))->toBeFalse();
        });
    });
});
