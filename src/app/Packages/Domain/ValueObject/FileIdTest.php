<?php

declare(strict_types=1);

use App\Packages\Domain\ValueObject\FileId;

describe('FileId', function () {
    describe('constructor', function () {
        it('accepts a valid UUID', function () {
            $id = new FileId('550e8400-e29b-41d4-a716-446655440000');

            expect($id->value())->toBe('550e8400-e29b-41d4-a716-446655440000');
        });

        it('throws when given an invalid format', function () {
            new FileId('not-a-uuid');
        })->throws(InvalidArgumentException::class);

        it('throws when given an empty string', function () {
            new FileId('');
        })->throws(InvalidArgumentException::class);
    });

    describe('generate()', function () {
        it('creates a valid UUID', function () {
            $id = FileId::generate();

            expect($id->value())->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
        });

        it('generates unique values each time', function () {
            $a = FileId::generate();
            $b = FileId::generate();

            expect($a->value())->not->toBe($b->value());
        });
    });

    describe('equals()', function () {
        it('returns true for the same UUID', function () {
            $a = new FileId('550e8400-e29b-41d4-a716-446655440000');
            $b = new FileId('550e8400-e29b-41d4-a716-446655440000');

            expect($a->equals($b))->toBeTrue();
        });

        it('returns false for different UUIDs', function () {
            $a = new FileId('550e8400-e29b-41d4-a716-446655440000');
            $b = new FileId('660f9511-f30c-52e5-b827-557766551111');

            expect($a->equals($b))->toBeFalse();
        });
    });
});