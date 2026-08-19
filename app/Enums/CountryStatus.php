<?php

namespace App\Enums;

enum CountryStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return __('enum.' . $this->value);
    }
}
