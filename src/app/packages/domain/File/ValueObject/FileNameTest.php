<?php

declare(strict_types=1);

use App\Packages\Domain\File\Exception\InvalidFileNameException;
use App\Packages\Domain\File\ValueObject\FileName;

describe('FileName', function () {
    describe('constructor', function () {
        it('accepts a normal file name', function () {
            $name = new FileName('document.pdf');

            expect($name->value())->toBe('document.pdf');
        });

        it('accepts a name at exactly 255 characters', function () {
            $name = new FileName(str_repeat('a', 254) . '.txt');

            expect(mb_strlen($name->value()))->toBe(255);
        });

        it('throws when given an empty string', function () {
            new FileName('');
        })->throws(InvalidFileNameException::class, 'File name cannot be empty.');

        it('throws when name exceeds 255 characters', function () {
            new FileName(str_repeat('a', 256));
        })->throws(InvalidFileNameException::class, 'must not exceed 255 characters');
    });

    describe('extension()', function () {
        it('returns the file extension', function () {
            $name = new FileName('photo.jpeg');

            expect($name->extension())->toBe('jpeg');
        });

        it('returns empty string when no extension', function () {
            $name = new FileName('Makefile');

            expect($name->extension())->toBe('');
        });
    });

    describe('equals()', function () {
        it('returns true for identical names', function () {
            $a = new FileName('file.txt');
            $b = new FileName('file.txt');

            expect($a->equals($b))->toBeTrue();
        });

        it('returns false for different names', function () {
            $a = new FileName('file.txt');
            $b = new FileName('other.txt');

            expect($a->equals($b))->toBeFalse();
        });
    });
});