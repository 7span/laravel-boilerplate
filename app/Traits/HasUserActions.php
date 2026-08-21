<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

trait HasUserActions
{
    /**
     * Boot the trait to handle automatic setting of `created_by`, `updated_by`, and `deleted_by`.
     */
    protected static function bootHasUserActions(): void
    {
        static::creating(fn (Model $model) => self::setAuthUserId($model, 'created_by'));
        static::updating(fn (Model $model) => self::setAuthUserId($model, 'updated_by'));
        static::deleting(fn (Model $model) => self::setAuthUserId($model, 'deleted_by'));
    }

    private static function setAuthUserId(Model $model, string $column): void
    {
        if (! Auth::check() || ! Schema::hasColumn($model->getTable(), $column)) {
            return;
        }

        $model->setAttribute($column, Auth::id());
    }
}
