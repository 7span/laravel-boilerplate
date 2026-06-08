<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use App\Traits\PaginationTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SettingService
{
    use PaginationTrait;

    private Setting $settingObj;

    public function __construct()
    {
        $this->settingObj = new Setting;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return LengthAwarePaginator<int, \Illuminate\Database\Eloquent\Model>|Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function collection(array $inputs): LengthAwarePaginator|Collection
    {
        $settings = $this->settingObj->getQB();

        if (! Auth::guard('api')->check()) {
            $settings = $settings->where('is_public', true);
        }

        /** @phpstan-ignore-next-line */
        return $this->paginationAttribute($settings);
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function update(array $inputs): array
    {
        $settings = $this->settingObj->getQB()
            ->whereIn('key', array_keys($inputs))
            ->get()
            ->keyBy('key');

        foreach ($inputs as $key => $value) {
            $item = $settings->get($key);
            if ($item !== null) {
                $item->update(['value' => $value]);
            }
        }

        $data['message'] = __('entity.entityUpdated', ['entity' => 'Master Setting']);

        return $data;
    }
}
