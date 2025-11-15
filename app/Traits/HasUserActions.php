<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasUserActions
{
    /**
     * Boot the trait to handle automatic setting of `created_by`, `updated_by`, and `deleted_by`.
     */
    protected static function bootHasUserActions(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = Auth::id();
            }
        });
    }
}
