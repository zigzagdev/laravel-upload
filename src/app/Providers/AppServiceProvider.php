<?php

namespace App\Providers;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Storage::extend('gcs', function (Application $app, array $config): Filesystem {
            $client = new StorageClient([
                'projectId'   => $config['project_id'],
                'keyFilePath' => $config['key_file_path'] ?? null,
            ]);

            $adapter = new GoogleCloudStorageAdapter(
                $client->bucket($config['bucket']),
                $config['path_prefix'] ?? '',
            );

            return new Filesystem($adapter);
        });
    }
}
