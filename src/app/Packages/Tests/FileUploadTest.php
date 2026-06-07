<?php

declare(strict_types=1);

use App\Packages\Application\UseCase\UploadFileUseCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('File Upload Integration', function () {
    beforeEach(function () {
        Storage::fake('s3');
    });

    it('returns 201 on successful upload', function () {
        $this->mock(UploadFileUseCase::class)
            ->shouldReceive('handle')
            ->once();

        $this->postJson('/api/v1/file-upload', [
            'file'           => UploadedFile::fake()->create('photo.jpg', 1024, 'image/jpeg'),
            'storage_driver' => 's3',
        ])->assertStatus(201);
    });

    it('returns 500 when use case throws an exception', function () {
        $this->mock(UploadFileUseCase::class)
            ->shouldReceive('handle')
            ->once()
            ->andThrow(new RuntimeException('Unexpected error'));

        $this->postJson('/api/v1/file-upload', [
            'file'           => UploadedFile::fake()->create('photo.jpg', 1024, 'image/jpeg'),
            'storage_driver' => 's3',
        ])->assertStatus(500)
          ->assertJson(['message' => 'Internal Server Error']);
    });

    it('returns 201 on successful video upload', function () {
        $this->mock(UploadFileUseCase::class)
            ->shouldReceive('handle')
            ->once();

        $this->postJson('/api/v1/file-upload', [
            'file'           => UploadedFile::fake()->create('video.mp4', 51200, 'video/mp4'),
            'storage_driver' => 'gcs',
        ])->assertStatus(201);
    });
});
