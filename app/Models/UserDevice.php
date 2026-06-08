<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use BaseModel;

    protected $fillable = [
        'user_id',
        'onesignal_player_id',
        'device_id',
        'device_type',
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\UserDevice> */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<User, UserDevice> $rel @phpstan-ignore varTag.type */
        $rel = $this->belongsTo(User::class);

        return $rel;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }
}
