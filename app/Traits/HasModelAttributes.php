<?php

namespace App\Traits;

use ReflectionClass;
use App\Attributes\Fillable;
use App\Attributes\Hidden;

trait HasModelAttributes
{
    public function initializeHasModelAttributes()
    {
        $reflection = new ReflectionClass($this);

        // Handle Fillable
        $fillableAttr = $reflection->getAttributes(Fillable::class);
        if (!empty($fillableAttr)) {
            $this->fillable = $fillableAttr[0]->newInstance()->fields;
        }

        // Handle Hidden
        $hiddenAttr = $reflection->getAttributes(Hidden::class);
        if (!empty($hiddenAttr)) {
            $this->hidden = $hiddenAttr[0]->newInstance()->fields;
        }
    }
}