<?php

declare(strict_types=1);

use App\Packages\Domain\File\ValueObject\StorageDriver;

describe('StorageDriver', function () {
    it('has S3 case with value "s3"', function () {
        expect(StorageDriver::S3->value)->toBe('s3');
    });

    it('has GCS case with value "gcs"', function () {
        expect(StorageDriver::GCS->value)->toBe('gcs');
    });

    it('can be created from a valid string', function () {
        expect(StorageDriver::from('s3'))->toBe(StorageDriver::S3);
        expect(StorageDriver::from('gcs'))->toBe(StorageDriver::GCS);
    });

    it('returns null for an invalid string via tryFrom', function () {
        expect(StorageDriver::tryFrom('azure'))->toBeNull();
    });
});