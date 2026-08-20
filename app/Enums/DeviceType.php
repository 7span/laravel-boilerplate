<?php

namespace App\Enums;

enum DeviceType: string
{
    case ANDROID = 'android';
    case IOS = 'ios';
    case WEB = 'web';

    public function label(): string
    {
        return __('enum.' . $this->value);
    }
}
