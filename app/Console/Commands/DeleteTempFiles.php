<?php

namespace App\Console\Commands;

use App\Models\TempFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;

#[Signature('media:delete-temp-files')]
#[Description('Delete expired temp files from all disks and remove their temp file records.')]
class DeleteTempFiles extends Command
{
    public function handle(): int
    {
        $expiryDays = max(1, (int) config('media.temp_file_delete_after_days', 2));

        $query = TempFile::whereIn('disk', ['s3', 'local', 'public'])
            ->where('created_at', '<', now()->subDays($expiryDays));

        if ($query->doesntExist()) {
            $this->info('No expired temp files found for deletion.');

            return self::SUCCESS;
        }

        $hasFailures = false;

        $query->chunkById(100, function ($tempFiles) use (&$hasFailures) {
            foreach ($tempFiles as $tempFile) {
                if (! $this->deleteTempFile($tempFile)) {
                    $hasFailures = true;
                }
            }
        });

        $this->info('Completed deletion of expired temp files.');

        return $hasFailures ? self::FAILURE : self::SUCCESS;
    }

    private function deleteTempFile(TempFile $tempFile): bool
    {
        $path = collect([$tempFile->directory, $tempFile->file_name])->filter()->map(fn ($part) => trim($part, '/'))->implode('/');
        $disk = $tempFile->disk;

        try {
            $storage = Storage::disk($disk);
            $storage->delete($path);

            if (in_array($disk, ['local', 'public']) && $storage->exists($path)) {
                $this->error("Failed to delete {$disk} file: {$path}");
                Log::error('Failed to delete local/public file', ['disk' => $disk, 'path' => $path, 'temp_file_id' => $tempFile->id]);

                return false;
            }

            $tempFile->delete();

            $this->info("Deleted {$disk} file: {$path} and removed temp record.");

            return true;
        } catch (\Throwable $e) {
            $this->error("Failed to delete file: {$path} from {$disk}. Error: {$e->getMessage()}");
            Log::error('Failed to delete file', ['disk' => $disk, 'path' => $path, 'temp_file_id' => $tempFile->id, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
