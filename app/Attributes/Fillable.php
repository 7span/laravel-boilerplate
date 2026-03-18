<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Fillable
{
    public function __construct(public array $fields) {}
}