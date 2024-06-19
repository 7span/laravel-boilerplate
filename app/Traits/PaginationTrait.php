<?php

namespace App\Traits;

trait PaginationTrait
{
    public function paginationAttribute($data)
    {
        $limit = request()->get('limit');
        $limit = $limit ?? config('site.pagination.limit');

        return (isset($limit) && $limit == '-1') ? $data->get() : $data->paginate($limit);
    }
}
