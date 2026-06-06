<?php

declare(strict_types=1);

use App\Packages\Domain\ValueObject\FilePath;
use App\Packages\Domain\ValueObject\MimeType;
use App\Packages\Infrastructure\Adapter\GCSAdapter;
use League\Flysystem\Filesystem;

test('upload calls filesystem write with correct arguments', function () {
    $filesystem = $this->createMock(Filesystem::class);
    $path       = new FilePath('uploads/test.jpg');
    $contents   = 'binary-data';
    $mimeType   = new MimeType('image/jpeg');

    $filesystem->expects($this->once())
        ->method('write')
        ->with(
            $path->value(),
            $contents,
            [
                'predefinedAcl' => 'publicRead',
                'metadata'      => ['contentType' => $mimeType->value()],
            ],
        );

    $adapter = new GCSAdapter($filesystem);
    $adapter->upload($path, $contents, $mimeType);
});