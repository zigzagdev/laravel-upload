<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFileModel extends Model
{
    protected $primaryKey = 'file_id';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = [
        'file_id',
        'file_name',
        'file_size',
        'mime_type',
        'file_path',
        'storage_driver',
    ];
}
