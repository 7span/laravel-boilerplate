<?php

declare(strict_types=1);

namespace App\Http\Resources\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = Country::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
