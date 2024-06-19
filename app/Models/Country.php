<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use BaseModel,HasFactory;

    public $queryable = [
        'id',
    ];

    protected $fillable = [
        'iso',
        'name',
        'iso3',
        'numcode',
        'phonecode',
    ];

    protected $relationship = [];
}
