<?php

declare(strict_types=1);

namespace App\Http\Resources\Setting;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Setting $resource
 */
#[SchemaName('Setting')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = Setting::class;

    /**
<<<<<<< HEAD
     * @return array<string, mixed>
=======
     * @return array{
     *     id: int,
     *     key: string,
     *     value: mixed,
     *     collection: string|null,
     *     is_public: bool|int,
     *     updated_by: int|null
     * }
>>>>>>> origin/master
     */
    public function toArray(Request $request): array
    {
        $data = $this->fields();

        return $data;
    }
}
