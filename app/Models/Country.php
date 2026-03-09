<?php

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use BaseModel,Mediable;

    protected $fillable = [
        'name',
        'iso',
        'iso3',
        'calling_code',
        'currency',
        'icon',
        'status',
    ];

    protected $defaultSort = 'name';

    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ],
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
