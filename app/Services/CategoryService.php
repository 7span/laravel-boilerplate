<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\PaginationTrait;

class CategoryService
{
    use PaginationTrait;

    private Category $categoryObj;

    public function __construct()
    {
        $this->categoryObj = new Category;
    }

    public function collection()
    {
        $query = $this->categoryObj->getQB();

        return $this->paginationAttribute($query);
    }

    public function store(array $data): Category
    {
        return $this->categoryObj->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function destroy(Category $category): void
    {
        $category->delete();
    }
}

