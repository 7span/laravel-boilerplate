<?php

namespace App\Library;

class Helper
{
    public static function getModelNameFromClassName($class)
    {
        return last(explode('\\', ($class)));
    }
}
