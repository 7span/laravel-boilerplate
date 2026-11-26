<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    /**
     * Paginate the given query using the `limit` query parameter.
     *
     * Passing `limit=-1` returns the whole result set without pagination.
     *
     * @template TModel of Model
     *
     * @param  QueryBuilder<TModel>|Builder<TModel>  $query
     * @return LengthAwarePaginator<int, TModel>|Collection<int, TModel>
     */
    protected function paginationAttribute(QueryBuilder|Builder $query): LengthAwarePaginator|Collection
    {
        $limit = request()->get('limit', config('site.pagination_limit'));

        return (string) $limit === '-1' ? $query->get() : $query->paginate((int) $limit);
    }
}
