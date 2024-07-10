<?php

namespace App\Services;

use App\Models\MasterSetting;
use App\Traits\PaginationTrait;

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

        return $this->paginationAttribute($masterSettings);
    }

    public function resource($id)
    {
        $masterSetting = $this->masterSettingObj->getQB()->findOrFail($id);

        return $masterSetting;
    }
}
