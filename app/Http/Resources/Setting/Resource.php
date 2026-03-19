<?php

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

    protected $model = Setting::class;

    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the setting.
             */
            'id' => $this->id,
            /**
             * The unique setting key used to reference this setting.
             */
            'key' => $this->key,
            /**
             * The value stored for this setting.
             */
            'value' => $this->value,
            /**
             * The collection/group this setting belongs to.
             */
            'collection' => $this->collection,
            /**
             * Whether this setting is publicly accessible without authentication.
             */
            'is_public' => $this->is_public,
            /**
             * The ID of the user who last updated this setting.
             */
            'updated_by' => $this->updated_by,
        ];
    }
}
