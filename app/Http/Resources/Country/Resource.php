<?php

namespace App\Http\Resources\Country;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Country $resource
 */
#[SchemaName('Country')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string<Country> */
    protected $model = Country::class;

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     iso: string|null,
     *     iso3: string|null,
     *     calling_code: string|null,
     *     currency: string|null,
     *     icon: string|null,
     *     status: string|null,
     *     created_at: int|null,
     *     updated_at: int|null
     * }
     */
    public function toArray(Request $request): array
    {
        return $this->fields();
    }
}
