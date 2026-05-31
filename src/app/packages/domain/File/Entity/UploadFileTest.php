<?php

declare(strict_types=1);

use App\Packages\Domain\File\Entity\UploadFile;
use App\Packages\Domain\File\ValueObject\FileId;
use App\Packages\Domain\File\ValueObject\FileName;
use App\Packages\Domain\File\ValueObject\FileSize;
use App\Packages\Domain\File\ValueObject\FilePath;
use App\Packages\Domain\File\ValueObject\MimeType;
use App\Packages\Domain\File\ValueObject\StorageDriver;

describe('UploadFile', function () {
    $defaults = fn () => [
        'fileName'      => new FileName('photo.jpeg'),
        'fileSize'      => new FileSize(204800),
        'mimeType'      => new MimeType('image/jpeg'),
        'filePath'      => new FilePath('uploads/2024/photo.jpeg'),
        'storageDriver' => StorageDriver::S3,
    ];

    describe('create()', function () use ($defaults) {
        it('creates an entity with an auto-generated FileId', function () use ($defaults) {
            $d = $defaults();
            $file = UploadFile::create(
                $d['fileName'],
                $d['fileSize'],
                $d['mimeType'],
                $d['filePath'],
                $d['storageDriver'],
            );

            expect($file->fileId()->value())
                ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
        });

        it('generates different FileIds for each call', function () use ($defaults) {
            $d = $defaults();
            $a = UploadFile::create($d['fileName'], $d['fileSize'], $d['mimeType'], $d['filePath'], $d['storageDriver']);
            $b = UploadFile::create($d['fileName'], $d['fileSize'], $d['mimeType'], $d['filePath'], $d['storageDriver']);

            expect($a->fileId()->value())->not->toBe($b->fileId()->value());
        });

        it('stores all provided values', function () use ($defaults) {
            $d = $defaults();
            $file = UploadFile::create(
                $d['fileName'],
                $d['fileSize'],
                $d['mimeType'],
                $d['filePath'],
                $d['storageDriver'],
            );

            expect($file->fileName()->value())->toBe('photo.jpeg');
            expect($file->fileSize()->value())->toBe(204800);
            expect($file->mimeType()->value())->toBe('image/jpeg');
            expect($file->filePath()->value())->toBe('uploads/2024/photo.jpeg');
            expect($file->storageDriver())->toBe(StorageDriver::S3);
        });
    });

    describe('reconstitute()', function () use ($defaults) {
        it('restores an entity with the given FileId', function () use ($defaults) {
            $d = $defaults();
            $fileId = new FileId('550e8400-e29b-41d4-a716-446655440000');

            $file = UploadFile::reconstitute(
                $fileId,
                $d['fileName'],
                $d['fileSize'],
                $d['mimeType'],
                $d['filePath'],
                $d['storageDriver'],
            );

            expect($file->fileId()->value())->toBe('550e8400-e29b-41d4-a716-446655440000');
        });

        it('preserves all values exactly', function () use ($defaults) {
            $d = $defaults();
            $fileId = new FileId('550e8400-e29b-41d4-a716-446655440000');

            $file = UploadFile::reconstitute(
                $fileId,
                $d['fileName'],
                $d['fileSize'],
                $d['mimeType'],
                $d['filePath'],
                StorageDriver::GCS,
            );

            expect($file->storageDriver())->toBe(StorageDriver::GCS);
            expect($file->fileName()->value())->toBe('photo.jpeg');
        });
    });
});