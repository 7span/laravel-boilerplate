<?php

namespace App\Services;

use App\Models\Setting;
use App\Traits\PaginationTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SettingService
{
    use PaginationTrait;

    private Setting $settingObj;

    public function __construct()
    {
        $this->settingObj = new Setting;
    }

    /**
     * Fetch the settings through the query builder so `filter`, `sort` and
     * `fields` apply. A guest only ever sees the public ones.
     *
     * @return LengthAwarePaginator<int, Setting>|Collection<int, Setting>
     */
    public function collection(): LengthAwarePaginator|Collection
    {
        $settings = $this->settingObj->getQB();

        if (! Auth::guard('api')->check()) {
            $settings->where('is_public', true);
        }

        return $this->paginationAttribute($settings);
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function update(array $inputs): array
    {
        $settings = $this->settingObj
            ->whereIn('key', array_keys($inputs))
            ->get();

        foreach ($settings as $setting) {
            $setting->update(['value' => $inputs[$setting->key]]);
        }

        return ['message' => __('entity.entityUpdated', ['entity' => 'Setting'])];
    }
}
