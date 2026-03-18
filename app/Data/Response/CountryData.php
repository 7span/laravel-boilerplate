<?php

declare(strict_types = 1);

namespace App\Data\Response;

use App\Models\Country;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class CountryData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $iso,
        public readonly ?string $iso3,
        public readonly string $calling_code,
        public readonly ?string $currency,
        public readonly ?string $icon,
        public readonly string $status,
        public readonly ?int $created_at,
        public readonly ?int $updated_at,
    ) {}

    /**
     * Build a CountryData instance from a Country Eloquent model.
     */
    public static function fromModel(Country $model): self
    {
        return new self(
            id: $model->id,
            name: $model->name,
            iso: $model->iso,
            iso3: $model->iso3,
            calling_code: $model->calling_code,
            currency: $model->currency,
            icon: $model->icon,
            status: $model->status,
            created_at: $model->created_at,
            updated_at: $model->updated_at,
        );
    }
}
