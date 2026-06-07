<?php

declare(strict_types=1);

use App\Packages\Application\UseCommand\UploadFileUseCommand;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

describe('UploadFileUseCommand', function () {
    beforeEach(function () {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($this->tmpFile, 'binary-data');

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalName')->andReturn('photo.jpg');
        $file->shouldReceive('getSize')->andReturn(1024);
        $file->shouldReceive('getMimeType')->andReturn('image/jpeg');
        $file->shouldReceive('getRealPath')->andReturn($this->tmpFile);

        $this->request = Mockery::mock(Request::class);
        $this->request->shouldReceive('file')->with('file')->andReturn($file);
        $this->request->shouldReceive('input')->with('storage_driver')->andReturn('s3');
        $this->request->shouldReceive('input')->with('metadata', [])->andReturn(['width' => 1920, 'height' => 1080]);
    });

    afterEach(function () {
        unlink($this->tmpFile);
        Mockery::close();
    });

    it('maps request values to properties correctly', function () {
        $command = new UploadFileUseCommand($this->request);

        expect($command->fileName)->toBe('photo.jpg');
        expect($command->fileSize)->toBe(1024);
        expect($command->mimeType)->toBe('image/jpeg');
        expect($command->filePath)->toBe('uploads/photo.jpg');
        expect($command->storageDriver)->toBe('s3');
        expect($command->contents)->toBe('binary-data');
        expect($command->metadata)->toBe(['width' => 1920, 'height' => 1080]);
    });

    it('defaults metadata to empty array when omitted', function () {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'binary-data');

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalName')->andReturn('photo.jpg');
        $file->shouldReceive('getSize')->andReturn(1024);
        $file->shouldReceive('getMimeType')->andReturn('image/jpeg');
        $file->shouldReceive('getRealPath')->andReturn($tmpFile);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('file')->with('file')->andReturn($file);
        $request->shouldReceive('input')->with('storage_driver')->andReturn('s3');
        $request->shouldReceive('input')->with('metadata', [])->andReturn([]);

        $command = new UploadFileUseCommand($request);

        expect($command->metadata)->toBe([]);
        unlink($tmpFile);
    });
});
