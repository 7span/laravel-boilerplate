<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Hidden
{
    public function __construct(public array $fields) {}
}