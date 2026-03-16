<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\Request as CategoryRequest;
use App\Http\Resources\Category\Resource as CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    use ApiResponser;

    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService;
    }

    #[OA\Get(
        path: '/api/categories',
        operationId: 'categoryIndex',
        tags: ['Category'],
        summary: 'List categories',
        x: ['model' => Category::class]
    )]
    public function index()
    {
        $categories = $this->categoryService->collection();

        return CategoryResource::collection($categories);
    }

    #[OA\Post(
        path: '/api/categories',
        operationId: 'categoryStore',
        tags: ['Category'],
        summary: 'Create category',
        security: [['bearerAuth' => []]]
    )]
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $category = $this->categoryService->store($data);

        return new CategoryResource($category);
    }

    #[OA\Get(
        path: '/api/categories/{category}',
        operationId: 'categoryShow',
        tags: ['Category'],
        summary: 'Show category',
        security: [['bearerAuth' => []]]
    )]
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    #[OA\Put(
        path: '/api/categories/{category}',
        operationId: 'categoryUpdate',
        tags: ['Category'],
        summary: 'Update category',
        security: [['bearerAuth' => []]]
    )]
    public function update(CategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());

        return new CategoryResource($category);
    }

    #[OA\Delete(
        path: '/api/categories/{category}',
        operationId: 'categoryDestroy',
        tags: ['Category'],
        summary: 'Delete category',
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Category $category)
    {
        $this->categoryService->destroy($category);

        return $this->success([], 'Category deleted successfully');
    }
}

