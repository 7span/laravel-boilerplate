<?php

namespace App\Traits;

use Spatie\LaravelData\Optional;

trait InteractsWithApiResponse
{
    public static function checkAppends($model, $attribute)
    {
        $requestedAppends = $model->getAppends();

        return in_array($attribute, $requestedAppends, true) ? $model->getAttribute($attribute) : Optional::create();
    }
}
