<?php

namespace App\Traits;

trait PaginationTrait
{
    public function paginationAttribute($inputs)
    {
        $inputs['limit'] = isset($inputs['limit']) ? $inputs['limit'] : config('site.pagination.limit');
        $inputs['page'] = (isset($inputs['page']) && $inputs['limit'] !== -1) ? $inputs['page'] : config('site.pagination.default_page');

        return $inputs;
    }
}
