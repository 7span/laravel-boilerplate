<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use BaseModel, HasFactory;

    protected $fillable = [
        'name',
        'iso3',
        'iso_code',
        'calling_code',
        'currency',
        'icon',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }

    public $queryable = [
        'id',
    ];

    public $defaultSort = 'name';

    public $allowedSorts = ['id', 'name'];
}
