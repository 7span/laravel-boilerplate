<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasModelAttributes;
use App\Attributes\Fillable;
use App\Attributes\Hidden;

#[Fillable(['id', 'user_id', 'sent_by', 'title', 'description', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'])]
#[Hidden(['id'])]
class Notification extends Model
{
    use BaseModel, HasModelAttributes;

    public $incrementing = false;

    protected $keyType = 'string';

    
    protected $casts = [
        'data' => 'array',
        'read_at' => 'timestamp',
        'created_at' => 'timestamp',
    ];

    protected $defaultSort = '-created_at';

    protected $scopedFilters = [
        'is_read',
    ];

    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'sender' => [
            'model' => User::class,
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeIsRead($query)
    {
        return $query->whereNotNull('read_at');
    }

}
