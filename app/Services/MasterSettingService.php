<?php

namespace App\Services;

use App\Models\MasterSetting;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;

class MasterSettingService
{
    use PaginationTrait;

    private MasterSetting $masterSettingObj;

    public function __construct()
    {
        $this->masterSettingObj = new MasterSetting;
    }

    public function collection(array $inputs)
    {
        $masterSettings = $this->masterSettingObj->getQB();

        if (! Auth::guard('sanctum')->check()) {
            $masterSettings = $masterSettings->where('is_public', true);
        }

        return $this->paginationAttribute($masterSettings);
    }

    public function resource($id)
    {
        $query = $this->masterSettingObj->getQB();

        if (! Auth::guard('sanctum')->check()) {
            $query->where('is_public', true);
        }

        $masterSetting = $query->findOrFail($id);

        return $masterSetting;
    }
}
