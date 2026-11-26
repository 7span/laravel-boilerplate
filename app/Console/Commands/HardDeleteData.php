<?php

namespace App\Console\Commands;

use SplFileInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Console\Attributes\Description;

#[Signature('system:hard-delete-data {--dry-run : Report what would be deleted without deleting it}')]
#[Description('Permanently delete soft deleted records older than the configured retention period.')]
class HardDeleteData extends Command
{
    public function handle(): int
    {
        $days = (int) config('site.soft_delete_retention_days');
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

            if ($count > 0 && ! $isDryRun) {
                $query->forceDelete();
            }

            $totalDeleted += $count;
            $this->line(class_basename($model) . ': ' . $count);
        }

        $this->info('Total: ' . $totalDeleted);

        return self::SUCCESS;
    }

    /**
     * Every model in app/Models that uses SoftDeletes, minus the excluded ones.
     *
     * The items are soft deleting models, a type PHP cannot express, so they stay
     * `mixed` rather than `Model` which would hide `onlyTrashed()`.
     *
     * @param  array<int, class-string<Model>>  $excludedModels
     * @return Collection<int, mixed>
     */
    protected function getApplicableModels(array $excludedModels): Collection
    {
        $excluded = collect($excludedModels);

        return collect(File::files(app_path('Models')))
            ->map(fn (SplFileInfo $file) => app()->getNamespace() . 'Models\\' . $file->getBasename('.php'))
            ->filter(fn (string $className) => (
                class_exists($className) &&
                ! $excluded->contains($className) &&
                collect(class_uses_recursive($className))->contains(SoftDeletes::class)
            ))
            ->map(fn (string $className) => app($className));
    }
}
