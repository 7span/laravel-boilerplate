<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait PaginationTrait
{

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Model>|\Illuminate\Database\Eloquent\Relations\Relation<Model, Model, mixed> $data
     * @return LengthAwarePaginator<int, Model>|Collection<int, Model>
     */
    public function paginationAttribute(mixed $data): LengthAwarePaginator|Collection
    {
        $limitParam = request()->get('limit');
        /** @var string|int $limit */
        $limit = ($limitParam !== null && (is_string($limitParam) || is_int($limitParam))) 
            ? $limitParam 
            : config('site.pagination_limit', 10);

        if ((string) $limit === '-1') {
            /** @var Collection<int, Model> $result */
            $result = $data->get(); // @phpstan-ignore-line
            return $result;
        }

        /** @phpstan-ignore-next-line */
        $result = $data->paginate((int) $limit);
        /** @var \Illuminate\Pagination\LengthAwarePaginator<int, Model> $result */
        return $result;
    }
}
