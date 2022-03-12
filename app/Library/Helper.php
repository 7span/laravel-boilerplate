<?php

namespace App\Library;

use Illuminate\Database\Eloquent\Model;

class Helper
{
    public static function getModelNameFromClassName($class)
    {
        return last( explode("\\", ($class) ) );
    }
}