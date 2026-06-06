<?php

declare(strict_types=1);

use App\Packages\Application\UseCommand\UploadFileUseCommand;
use App\Packages\Application\UseCase\UploadFileUseCase;
use App\Packages\Domain\Entity\UploadFile;
use App\Packages\Domain\Repository\CloudStorageInterface;
use App\Packages\Domain\Repository\FileRepositoryInterface;
use App\Packages\Domain\Repository\MongoFileRepositoryInterface;
use App\Packages\Domain\ValueObject\FileMetadata;
use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\Exception\InvalidFileSizeException;
use App\Packages\Domain\ValueObject\MimeType;

describe('UploadFileUseCase', function () {
    beforeEach(function () {
        $this->cloudStorage    = Mockery::mock(CloudStorageInterface::class);
        $this->fileRepository  = Mockery::mock(FileRepositoryInterface::class);
        $this->mongoRepository = Mockery::mock(MongoFileRepositoryInterface::class);

        $this->useCase = new UploadFileUseCase(
            $this->cloudStorage,
            $this->fileRepository,
            $this->mongoRepository,
        );

        $this->command = new UploadFileUseCommand(
            fileName:      'photo.jpg',
            fileSize:      1024,
            mimeType:      'image/jpeg',
            filePath:      'uploads/photo.jpg',
            storageDriver: 's3',
            contents:      'binary-data',
            metadata:      ['width' => 1920, 'height' => 1080],
        );
    });

    afterEach(function () {
        Mockery::close();
    });

    it('uploads file to cloud storage with correct path and mime type', function () {
        $this->cloudStorage
            ->shouldReceive('upload')
            ->once()
            ->withArgs(function (FilePath $path, string $contents, MimeType $mimeType) {
                return $path->value() === 'uploads/photo.jpg'
                    && $contents === 'binary-data'
                    && $mimeType->value() === 'image/jpeg';
            });

        $this->fileRepository->shouldReceive('save')->once()->with(Mockery::type(UploadFile::class));
        $this->mongoRepository->shouldReceive('save')->once();

        $this->useCase->handle($this->command);
    });

    it('saves file entity to MySQL repository', function () {
        $this->cloudStorage->shouldReceive('upload')->once();

        $this->fileRepository
            ->shouldReceive('save')
            ->once()
            ->withArgs(function (UploadFile $file) {
                return $file->fileName()->value() === 'photo.jpg'
                    && $file->fileSize()->value() === 1024
                    && $file->mimeType()->value() === 'image/jpeg'
                    && $file->filePath()->value() === 'uploads/photo.jpg'
                    && $file->storageDriver()->value === 's3';
            });

        $this->mongoRepository->shouldReceive('save')->once();

        $this->useCase->handle($this->command);
    });

    it('saves metadata to MongoDB repository', function () {
        $this->cloudStorage->shouldReceive('upload')->once();
        $this->fileRepository->shouldReceive('save')->once();

        $this->mongoRepository
            ->shouldReceive('save')
            ->once()
            ->withArgs(function ($fileId, FileMetadata $metadata) {
                return $metadata->value() === ['width' => 1920, 'height' => 1080];
            });

        $this->useCase->handle($this->command);
    });

    describe('video upload', function () {
        beforeEach(function () {
            $this->videoCommand = new UploadFileUseCommand(
                fileName:      'video.mp4',
                fileSize:      52_428_800, // 50MB
                mimeType:      'video/mp4',
                filePath:      'uploads/video.mp4',
                storageDriver: 's3',
                contents:      'video-binary-data',
                metadata:      ['duration' => 120, 'resolution' => '1920x1080'],
            );
        });

        it('uploads video to cloud storage with correct mime type', function () {
            $this->cloudStorage
                ->shouldReceive('upload')
                ->once()
                ->withArgs(function (FilePath $path, string $contents, MimeType $mimeType) {
                    return $path->value() === 'uploads/video.mp4'
                        && $contents === 'video-binary-data'
                        && $mimeType->value() === 'video/mp4';
                });

            $this->fileRepository->shouldReceive('save')->once()->with(Mockery::type(UploadFile::class));
            $this->mongoRepository->shouldReceive('save')->once();

            $this->useCase->handle($this->videoCommand);
        });

        it('saves video metadata to MongoDB', function () {
            $this->cloudStorage->shouldReceive('upload')->once();
            $this->fileRepository->shouldReceive('save')->once();

            $this->mongoRepository
                ->shouldReceive('save')
                ->once()
                ->withArgs(function ($fileId, FileMetadata $metadata) {
                    return $metadata->value() === ['duration' => 120, 'resolution' => '1920x1080'];
                });

            $this->useCase->handle($this->videoCommand);
        });

        it('rejects video exceeding 100MB', function () {
            $this->cloudStorage->shouldNotReceive('upload');
            $this->fileRepository->shouldNotReceive('save');
            $this->mongoRepository->shouldNotReceive('save');

            $command = new UploadFileUseCommand(
                fileName:      'large.mp4',
                fileSize:      104_857_601, // 100MB + 1byte
                mimeType:      'video/mp4',
                filePath:      'uploads/large.mp4',
                storageDriver: 's3',
                contents:      'binary-data',
            );

            $this->useCase->handle($command);
        })->throws(InvalidFileSizeException::class);
    });

    it('calls all three operations in order', function () {
        $order = [];

        $this->cloudStorage
            ->shouldReceive('upload')->once()
            ->andReturnUsing(function () use (&$order) { $order[] = 'upload'; });

        $this->fileRepository
            ->shouldReceive('save')->once()
            ->andReturnUsing(function () use (&$order) { $order[] = 'mysql'; });

        $this->mongoRepository
            ->shouldReceive('save')->once()
            ->andReturnUsing(function () use (&$order) { $order[] = 'mongo'; });

        $this->useCase->handle($this->command);

        expect($order)->toBe(['upload', 'mysql', 'mongo']);
    });
});