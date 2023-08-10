<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *s
     * @return \Inertia\Response
     */
    public function index()
    {
        $resources = $this->categoryService->getCategories();
        return Inertia::render('Category/Index', $resources);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function store(Request $request)
    {
        $errors = $this->categoryService->storeCategories($request->all());

        if (!empty($errors)) {
            $categories = Category::orderBy('id', 'desc')->get();
            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => $errors, 'prevRequestData' => $request])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        return to_route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $errors = $this->categoryService->updateCategory($request, $category);

        if (!empty($errors)) {
            $categories = Category::orderBy('id', 'desc')->get();

            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => $errors, 'errorId' => $category->id])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        return to_route('categories.index')->with(['updatedId' => $category->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);
        return to_route('categories.index');
    }

    /**
     * Keep the specified resource into the cache.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function keepCache(Request $request)
    {
        $cacheData = $request->all();

        $errors = [];
        foreach ($cacheData as $data) {
            $validator = $this->categoryService->validateName($data);

            if ($validator->fails()) {
                $errors[] = $validator->errors();
            }
        }
        if (empty($errors)) {
            Cache::forever('categories', $cacheData);
        } else {
            $categories = Category::orderBy('id', 'desc')->get();

            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => $errors, 'prevRequestData' => $request])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        return to_route('categories.index');
    }

    /**
     * Get the specified resource into the cache.
     *
     * @return \Inertia\Response
     */
    public function getCache()
    {
        $cacheData = Cache::get('categories');

        $names = [];
        if (empty($cacheData))
            return Inertia::render('Category/CacheList', ['cacheData' => []]);
        else
            $names = array_values($cacheData);

        return Inertia::render('Category/CacheList', ['cacheData' => $names]);
    }

    /**
     * insert the specified resource into the cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insertCategoriesFromCache()
    {
        $cacheData = Cache::get('categories');

        if (!isset($cacheData))
            to_route('categories.cache_list');

        $error = $this->categoryService->bulkInsert($cacheData);

        if (empty($error))
            Cache::forget('categories');

        return to_route('categories.cache_list');
    }

    /**
     * Clear the specified resource into the cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        Cache::forget('categories');
        return to_route('categories.cache_list');
    }
}
