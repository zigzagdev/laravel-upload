<?php

declare(strict_types=1);

use App\Models\UploadFileModel;
use App\Packages\Domain\Entity\UploadFile;
use App\Packages\Domain\ValueObject\FileName;
use App\Packages\Domain\ValueObject\FileSize;
use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;
use App\Packages\Domain\ValueObject\StorageDriver;
use App\Packages\Infrastructure\FileRepository;

test('save calls newInstance with correct attributes', function () {
    $file = UploadFile::create(
        new FileName('test.jpg'),
        new FileSize(1024),
        new MimeType('image/jpeg'),
        new FilePath('uploads/test.jpg'),
        StorageDriver::S3,
    );

    $instance = $this->createMock(UploadFileModel::class);
    $instance->expects($this->once())->method('save');

    $model = $this->createMock(UploadFileModel::class);
    $model->expects($this->once())
        ->method('newInstance')
        ->with([
            'file_id'        => $file->fileId()->value(),
            'file_name'      => 'test.jpg',
            'file_size'      => 1024,
            'mime_type'      => 'image/jpeg',
            'file_path'      => 'uploads/test.jpg',
            'storage_driver' => 's3',
        ])
        ->willReturn($instance);

    $repository = new FileRepository($model);
    $repository->save($file);
});