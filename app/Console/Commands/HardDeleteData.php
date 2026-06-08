<?php

declare(strict_types=1);

namespace App\Console\Commands;

use SplFileInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HardDeleteData extends Command
{
    protected $signature = 'system:hard-delete-data {--dry-run}';

    protected $description = 'Permanently delete soft-deleted data from the database after a configured number of days.';

    public function handle(): int
    {
        $val = Config::get('site.soft_delete_retention_days', 0);
        $days = is_scalar($val) ? (int) $val : 0;
        $cutoff = now()->subDays($days);
        /** @var bool $isDryRun */
        $isDryRun = $this->option('dry-run');
        $totalDeleted = 0;

        /**
         * Add models to exclude from
         * hard deletion here.
         * @var array<int, class-string<Model>> $excludedModels
         */
        $excludedModels = [
            // User::class,
        ];

        $this->info($isDryRun
            ? "Dry run: Would delete records older than {$days} days"
            : "Deleting records older than {$days} days");

        foreach ($this->getApplicableModels($excludedModels) as $model) {
            $modelClass = $model::class;
            /** @var \Illuminate\Database\Eloquent\Builder<Model> $query */
            $query = $modelClass::onlyTrashed()->where('deleted_at', '<=', $cutoff);
            $count = $query->count();

            if ($count > 0 && ! $isDryRun) {
                $query->forceDelete();
            }

            $totalDeleted += $count;
            $this->line(class_basename($modelClass) . ': ' . $count);
        }

        $this->info('Total: ' . $totalDeleted);

        return 0;
    }

    /**
     * @param array<int, class-string<Model>> $excludedModels
     * @return Collection<int, Model>
     */
    protected function getApplicableModels(array $excludedModels): Collection
    {
        $excluded = collect($excludedModels);

        /** @var Collection<int, Model> $models */
        $models = collect(File::files(app_path('Models')))
            ->map(fn (SplFileInfo $file) => 'App\\Models\\' . $file->getBasename('.php'))
            ->filter(fn (string $className) => (
                class_exists($className) &&
                ! $excluded->contains($className) &&
                collect(class_uses_recursive($className))->contains(SoftDeletes::class)
            ))
            ->map(fn (string $className) => app($className));

        return $models;
    }
}
