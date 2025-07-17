<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Aws\S3\S3Client;
use App\Models\TempFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:delete-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired temp files from all disks and remove their temp file records.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiryDays = (int) config('media.temp_file_delete_after_days', 2);

        $expiredTempFiles = TempFile::whereIn('disk', ['s3', 'local', 'public'])
            ->where('created_at', '<', Carbon::now()->subDays($expiryDays))
            ->get();

        if ($expiredTempFiles->isEmpty()) {
            $this->info('No expired temp files found for deletion.');

            return 0;
        }

        $s3Config = config('filesystems.disks.s3');
        $client = new S3Client([
            'version' => 'latest',
            'region' => $s3Config['region'],
            'credentials' => [
                'key' => $s3Config['key'],
                'secret' => $s3Config['secret'],
            ],
        ]);
        $bucket = $s3Config['bucket'];

        foreach ($expiredTempFiles as $tempFile) {
            $key = $tempFile->directory . '/' . $tempFile->file_name;
            try {
                if ($tempFile->disk === 's3') {
                    $client->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => $key,
                    ]);
                    $tempFile->delete();
                    $this->info("Deleted S3 file: {$key} and removed temp record.");
                    Log::info('Deleted S3 file and temp record', ['key' => $key]);
                } elseif (in_array($tempFile->disk, ['local', 'public'])) {
                    $deleted = Storage::disk($tempFile->disk)->delete($key);
                    if ($deleted) {
                        $tempFile->delete();
                        $this->info("Deleted {$tempFile->disk} file: {$key} and removed temp record.");
                        Log::info('Deleted local/public file and temp record', ['disk' => $tempFile->disk, 'key' => $key]);
                    } else {
                        $this->error("Failed to delete {$tempFile->disk} file: {$key}.");
                        Log::error('Failed to delete local/public file', ['disk' => $tempFile->disk, 'key' => $key]);
                    }
                } else {
                    $this->error("Unknown disk type: {$tempFile->disk} for file: {$key}.");
                    Log::warning('Unknown disk type for temp file', ['disk' => $tempFile->disk, 'key' => $key]);
                }
            } catch (\Exception $e) {
                $this->error("Failed to delete file: {$key} from {$tempFile->disk}. Error: {$e->getMessage()}");
                Log::error('Failed to delete file', ['disk' => $tempFile->disk, 'key' => $key, 'error' => $e->getMessage()]);
            }
        }

        $this->info('Completed deletion of expired non-synced S3 files.');

        return 0;
    }
}
