<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $disk
 * @property string $directory
 * @property string $file_name
 */
class TempFile extends Model
{
    protected $fillable = [
        'disk',
        'directory',
        'file_name',
    ];
}
