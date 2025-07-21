<?php

namespace App\Console\Commands;

use App\Models\MasterSetting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\SoftDeletes;

class HardDeleteData extends Command
{
    protected $signature = 'system:hard-delete-data {--dry-run}';
    protected $description = 'Permanently delete soft-deleted data from the database after a configured number of days.';

    public function handle()
    {
        $days = Config::get('site.soft_delete_retention_days', 3);
        $cutoff = now()->subDays($days);
        $isDryRun = $this->option('dry-run');
        $totalDeleted = 0;

        /**
         * Add models to exclude from 
         * hard deletion here.
         */
        $excludedModels = [
            // Country::class,  
        ];

        $this->info($isDryRun
            ? "Dry run: Would delete records older than {$days} days"
            : "Deleting records older than {$days} days");

        foreach ($this->getApplicableModels($excludedModels) as $model) {
            $query = $model::onlyTrashed()->where('deleted_at', '<=', $cutoff);
            $count = $query->count();

            if ($count > 0 && !$isDryRun) {
                $query->forceDelete();
            }

            $totalDeleted += $count;
            $this->line(class_basename($model::class) . ': ' . $count);
        }

        $this->info('Total: ' . $totalDeleted);
    }

    protected function getApplicableModels(array $excludedModels)
    {
        return collect(File::files(app_path('Models')))
            ->filter(fn($file) => $file->getExtension() === 'php')
            ->map(fn($file) => 'App\\Models\\' . $file->getBasename('.php'))
            ->filter(
                fn($class) =>
                class_exists($class) &&
                    !in_array($class, $excludedModels) &&
                    in_array(SoftDeletes::class, class_uses_recursive($class))
            )
            ->map(fn($class) => new $class);
    }
}