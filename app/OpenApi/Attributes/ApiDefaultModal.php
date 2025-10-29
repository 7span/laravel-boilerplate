<?php

namespace App\OpenApi\Attributes;

use OpenApi\Attributes\Response;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiDefaultModal
{
    public function __construct(public string $modelName)
    {
        // parent::__construct(response: 401, description: $description);
    }
}
