<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use BaseModel, HasEvents, HasFactory,SoftDeletes;

    protected $table = 'notifications';

    public $incrementing = false;

    public $fillable = [
        'id',
        'notifiable_type',
        'notifiable_id',
        'type',
        'data',
        'read_at',
        'title',
        'description',
        'notified_by',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = ['updated_at', 'deleted_at'];

    public $queryable = ['id'];

    protected $relationship = [
        'notifiedBy' => [
            'model' => 'App\Models\User',
        ],
    ];

    protected $allowedSorts = ['created_at'];

    public function setNotifiableTypeAttribute($value)
    {
        $this->attributes['notifiable_type'] = 'App\\Models\\' . $value;
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->attributes['created_at'] = strtotime($value);
    }

    public function getReadAtAttribute($value)
    {
        return $this->attributes['read_at'] = ! empty($value) ? strtotime($value) : null;
    }

    public function notifiedBy()
    {
        return $this->belongsTo(User::class, 'notified_by');
    }
}
