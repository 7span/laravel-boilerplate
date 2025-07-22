<?php

namespace App\Console\Commands;

use SplFileInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\SoftDeletes;

class HardDeleteData extends Command
{
    protected $signature = 'system:hard-delete-data {--dry-run}';
    protected $description = 'Permanently delete soft-deleted data from the database after a configured number of days.';

    public function handle()
    {
        $days = Config::get('site.soft_delete_retention_days');
        $cutoff = now()->subDays($days);
        $isDryRun = $this->option('dry-run');
        $totalDeleted = 0;

        /**
         * Add models to exclude from 
         * hard deletion here.
         */
        $excludedModels = [
            // User::class,
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
        $excluded = collect($excludedModels);
        return collect(File::files(app_path('Models')))
            ->map(fn(SplFileInfo $file) => app()->getNamespace() . 'Models\\' . $file->getBasename('.php'))
            ->filter(fn(string $className) => (
                class_exists($className) &&
                !$excluded->contains($className) &&
                collect(class_uses_recursive($className))->contains(SoftDeletes::class)
            ))
            ->map(fn(string $className) => app($className));
    }
}