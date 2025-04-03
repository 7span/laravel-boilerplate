<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-old-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old log files to free up storage and maintain a clean logging environment.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs'); // Path to the logs directory
        $files = File::files($logPath); // Get all files in the logs directory

        foreach ($files as $file) {
            // Check if the file is older than 1 month
            if (Carbon::parse(File::lastModified($file))->lt(Carbon::now()->subMonth())) {
                File::delete($file); // Delete the file
                $this->info("Deleted: {$file->getFilename()}");
            }
        }

        $this->info('Old log files cleared successfully.');
    }
}