<?php

namespace App\Services;

use App\Models\Setting;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;

class SettingService
{
    use PaginationTrait;

    private Setting $settingObj;

    public function __construct()
    {
        $this->settingObj = new Setting;
    }

    public function collection(array $inputs)
    {
        $settings = $this->settingObj->getQB();

        if (! Auth::guard('sanctum')->check()) {
            $settings = $settings->where('is_public', true);
        }

        return $this->paginationAttribute($settings);
    }

    public function update(array $inputs): array
    {
        foreach ($inputs as $key => $value) {
            $setting = $this->settingObj->where('key', $key)->firstOrFail();
            $setting->update(['value' => $value]);
        }

        $data['message'] = __('entity.entityUpdated', ['entity' => 'Master Setting']);

        return $data;
    }
}
