<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Attributes\Fillable;
use App\Attributes\Hidden;
use App\Traits\HasModelAttributes;

#[Fillable(['id', 'name', 'iso', 'iso3', 'calling_code', 'currency', 'icon', 'status'])]
#[Hidden(['id'])]
class Country extends Model
{
    use BaseModel, HasModelAttributes;

    protected $defaultSort = 'name';

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
