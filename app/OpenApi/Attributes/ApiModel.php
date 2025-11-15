<?php

namespace App\OpenApi\Attributes;

use OpenApi\Attributes\Response;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiModel
{
    public function __construct(public string $model)
    {
        // parent::__construct(response: 401, description: $description);
    }
}
