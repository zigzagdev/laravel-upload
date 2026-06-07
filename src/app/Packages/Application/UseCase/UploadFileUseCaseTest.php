<?php

declare(strict_types=1);

use App\Packages\Application\UseCommand\UploadFileUseCommand;
use App\Packages\Application\UseCase\UploadFileUseCase;
use App\Packages\Domain\Entity\UploadFile;
use App\Packages\Domain\Exception\InvalidFileSizeException;
use App\Packages\Domain\Repository\CloudStorageInterface;
use App\Packages\Domain\Repository\FileRepositoryInterface;
use App\Packages\Domain\Repository\MongoFileRepositoryInterface;
use App\Packages\Domain\ValueObject\FileMetadata;
use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

function makeCommand(array $overrides = []): UploadFileUseCommand
{
    $defaults = [
        'name'          => 'photo.jpg',
        'size'          => 1024,
        'mimeType'      => 'image/jpeg',
        'storageDriver' => 's3',
        'contents'      => 'binary-data',
        'metadata'      => ['width' => 1920, 'height' => 1080],
    ];
    $params = array_merge($defaults, $overrides);

    $tmpFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tmpFile, $params['contents']);

    $file = Mockery::mock(UploadedFile::class);
    $file->shouldReceive('getClientOriginalName')->andReturn($params['name']);
    $file->shouldReceive('getSize')->andReturn($params['size']);
    $file->shouldReceive('getMimeType')->andReturn($params['mimeType']);
    $file->shouldReceive('getRealPath')->andReturn($tmpFile);

    $request = Mockery::mock(Request::class);
    $request->shouldReceive('file')->with('file')->andReturn($file);
    $request->shouldReceive('input')->with('storage_driver')->andReturn($params['storageDriver']);
    $request->shouldReceive('input')->with('metadata', [])->andReturn($params['metadata']);

    return new UploadFileUseCommand($request);
}

describe('UploadFileUseCase', function () {
    beforeEach(function () {
        $this->cloudStorage    = Mockery::mock(CloudStorageInterface::class);
        $this->fileRepository  = Mockery::mock(FileRepositoryInterface::class);
        $this->mongoRepository = Mockery::mock(MongoFileRepositoryInterface::class);

        $this->useCase  = new UploadFileUseCase(
            $this->cloudStorage,
            $this->fileRepository,
            $this->mongoRepository,
        );

        $this->command = makeCommand();
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

    describe('video upload', function () {
        beforeEach(function () {
            $this->videoCommand = makeCommand([
                'name'     => 'video.mp4',
                'size'     => 52_428_800,
                'mimeType' => 'video/mp4',
                'metadata' => ['duration' => 120, 'resolution' => '1920x1080'],
            ]);
        });

        it('uploads video to cloud storage with correct mime type', function () {
            $this->cloudStorage
                ->shouldReceive('upload')
                ->once()
                ->withArgs(function (FilePath $path, string $contents, MimeType $mimeType) {
                    return $path->value() === 'uploads/video.mp4'
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

            $command = makeCommand([
                'name'     => 'large.mp4',
                'size'     => 104_857_601,
                'mimeType' => 'video/mp4',
            ]);

            $this->useCase->handle($command);
        })->throws(InvalidFileSizeException::class);
    });
});
