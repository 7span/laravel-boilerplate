<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Enums\CountryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/** @property CountryStatus|null $status */
#[Fillable([
    'name',
    'iso',
    'iso3',
    'calling_code',
    'currency',
    'icon',
    'status',
])]
class Country extends Model
{
    use BaseModel;
    use SoftDeletes;

    protected string $defaultSort = 'name';

    /** @var array<int, string> */
    protected array $exactFilters = [
        'iso',
        'iso3',
        'status',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => CountryStatus::class,
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
