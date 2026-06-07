<?php

declare(strict_types=1);

namespace App\Packages\Application\UseCommand;

use Illuminate\Http\Request;

final class UploadFileUseCommand
{
    public readonly string $fileName;
    public readonly int    $fileSize;
    public readonly string $mimeType;
    public readonly string $filePath;
    public readonly string $storageDriver;
    public readonly string $contents;
    public readonly array  $metadata;

    public function __construct(Request $request)
    {
        $file = $request->file('file');

        $this->fileName      = $file->getClientOriginalName();
        $this->fileSize      = $file->getSize();
        $this->mimeType      = $file->getMimeType();
        $this->filePath      = 'uploads/' . $file->getClientOriginalName();
        $this->storageDriver = $request->input('storage_driver');
        $this->contents      = file_get_contents($file->getRealPath());
        $this->metadata      = $request->input('metadata', []);
    }
}
