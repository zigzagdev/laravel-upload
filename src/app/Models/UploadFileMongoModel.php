<?php

declare(strict_types=1);

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UploadFileMongoModel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'upload_files';

    protected $fillable = [
        'file_id',
        'file_name',
        'file_size',
        'mime_type',
        'file_path',
        'storage_driver',
    ];
}