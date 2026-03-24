<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use BaseModel;

    protected $fillable = [
        'name',
        'iso',
        'iso3',
        'iso_code',
        'calling_code',
        'currency',
        'icon',
        'status',
    ];

    protected string $defaultSort = 'name';

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
