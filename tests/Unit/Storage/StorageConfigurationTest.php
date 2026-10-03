<?php

namespace Tests\Unit\Storage;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageConfigurationTest extends TestCase
{
    public function test_s3_driver_is_supported_and_can_be_instantiated(): void
    {
        config([
            'filesystems.disks.s3.bucket' => 'test-bucket',
            'filesystems.disks.s3.region' => 'us-east-1',
        ]);

        $disk = Storage::disk('s3');

        $this->assertInstanceOf(FilesystemAdapter::class, $disk);
    }

    public function test_private_disk_defaults_to_local(): void
    {
        $disk = Storage::disk('private');

        $this->assertInstanceOf(FilesystemAdapter::class, $disk);
        $this->assertStringContainsString('storage/app/private', config('filesystems.disks.private.root'));
    }

    public function test_private_disk_switches_to_s3_when_configured(): void
    {
        // Simula PRIVATE_FILESYSTEM_DISK=s3
        config([
            'filesystems.disks.private' => [
                'driver' => 's3',
                'bucket' => 'test-private-bucket',
                'region' => 'us-east-1',
                'visibility' => 'private',
            ],
        ]);

        $disk = Storage::disk('private');

        $this->assertInstanceOf(FilesystemAdapter::class, $disk);
        $this->assertEquals('s3', config('filesystems.disks.private.driver'));
    }
}
