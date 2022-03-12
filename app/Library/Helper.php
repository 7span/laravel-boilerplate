<?php

namespace App\Library;

use Illuminate\Database\Eloquent\Model;

class Helper{
    public static function getModelNameFromClassObj(Model $classObj)
    {
        return last( explode("\\", get_class($classObj) ) );
    }
}