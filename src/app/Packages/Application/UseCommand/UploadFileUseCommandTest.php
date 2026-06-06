<?php

declare(strict_types=1);

use App\Packages\Application\UseCommand\UploadFileUseCommand;

describe('UploadFileUseCommand', function () {
    it('holds all primitives as given', function () {
        $command = new UploadFileUseCommand(
            fileName:      'photo.jpg',
            fileSize:      1024,
            mimeType:      'image/jpeg',
            filePath:      'uploads/photo.jpg',
            storageDriver: 's3',
            contents:      'binary-data',
            metadata:      ['width' => 1920, 'height' => 1080],
        );

        expect($command->fileName)->toBe('photo.jpg');
        expect($command->fileSize)->toBe(1024);
        expect($command->mimeType)->toBe('image/jpeg');
        expect($command->filePath)->toBe('uploads/photo.jpg');
        expect($command->storageDriver)->toBe('s3');
        expect($command->contents)->toBe('binary-data');
        expect($command->metadata)->toBe(['width' => 1920, 'height' => 1080]);
    });

    it('defaults metadata to empty array when omitted', function () {
        $command = new UploadFileUseCommand(
            fileName:      'photo.jpg',
            fileSize:      1024,
            mimeType:      'image/jpeg',
            filePath:      'uploads/photo.jpg',
            storageDriver: 's3',
            contents:      'binary-data',
        );

        expect($command->metadata)->toBe([]);
    });
});