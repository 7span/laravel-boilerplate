<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempFile extends Model
{
    protected $fillable = [
        'disk',
        'directory',
        'file_name'
    ];
}
