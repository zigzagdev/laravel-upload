<?php

declare(strict_types=1);

use App\Models\UploadFileMongoModel;
use App\Packages\Domain\ValueObject\FileId;
use App\Packages\Domain\ValueObject\FileMetadata;
use App\Packages\Infrastructure\MongoFileRepository;

test('save calls newInstance with file_id and metadata', function () {
    $fileId   = FileId::generate();
    $metadata = new FileMetadata(['width' => 1920, 'height' => 1080]);

    $instance = $this->createMock(UploadFileMongoModel::class);
    $instance->expects($this->once())->method('save');

    $model = $this->createMock(UploadFileMongoModel::class);
    $model->expects($this->once())
        ->method('newInstance')
        ->with([
            'file_id'  => $fileId->value(),
            'metadata' => ['width' => 1920, 'height' => 1080],
        ])
        ->willReturn($instance);

    $repository = new MongoFileRepository($model);
    $repository->save($fileId, $metadata);
});