<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class LogCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete log files older than the configured number of days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = Config::get('logging.log_retention_days', 30);
        $logPath = storage_path('logs');
        $deleted = 0;

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            $now = now();
            foreach ($files as $file) {
                if ($file->getExtension() === 'log' && $now->diffInDays(Carbon::createFromTimestamp($file->getMTime())) > $days) {
                    File::delete($file->getRealPath());
                    $deleted++;
                }
            }
        }

        $this->info("Deleted {$deleted} log file(s) older than {$days} days.");
    }
}
