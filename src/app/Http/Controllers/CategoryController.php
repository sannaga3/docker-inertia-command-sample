<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *s
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updatedId = Session::get('updatedId') ? intval(Session::get('updatedId')) : null;
        $categories = Category::orderBy('id', 'desc')
            ->whereNull('deleted_at')
            ->get();

        if ($updatedId)
            return Inertia::render('Category/Index', ['categories' => $categories, 'updatedId' => $updatedId]);

        return Inertia::render('Category/Index', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $names = $request->all();

        $errors = [];
        foreach ($names as $name) {
            $validator = $this->validateName($name);

            if ($validator->fails()) {
                $errors[] = $validator->errors();
            }
        }

        if (!empty($errors)) {
            $categories = Category::orderBy('id', 'desc')->get();

            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => $errors, 'prevRequestData' => $request])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        $errorMessage = $this->bulkInsert($names);

        if (isset($errorMessage)) {
            $error = '';
            if (false !== strpos($errorMessage, 'Duplicate entry'))
                $error = "The name field has a duplicate value.";
            else
                $error = "The name field is required.";

            $categories = Category::orderBy('id', 'desc')->get();
            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => [$error], 'prevRequestData' => $request])
                ->toResponse($request)
                ->setStatusCode(400);
        }

        return to_route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validator = $this->validateName($request->name);
            $category->update($request->all());
        } catch (Exception) {
            $errors = $validator->errors();
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
     * @param  App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        $taskCategories = TaskCategory::where('category_id', $category->id)
            ->whereNull('deleted_at')
            ->get();
        TaskCategory::destroy($taskCategories);
        DB::commit();

        $category->delete();
        return to_route('categories.index');
    }

    /**
     * Validate request name.
     *
     * @param string $name
     * @return \Illuminate\Validation\Validator $validator
     */
    public function validateName($name)
    {
        $validator = Validator::make(['name' => $name], [
            'name' => 'required|unique:categories',
        ]);
        return $validator;
    }

    /**
     * Keep the specified resource into the cache.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function keepCache(Request $request)
    {
        $cacheData = $request->all();

        $errors = [];
        foreach ($cacheData as $data) {
            $validator = $this->validateName($data);

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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function insertCache()
    {
        $cacheData = Cache::get('categories');

        if (!isset($cacheData))
            Inertia::render('Category/CacheList');

        $error = $this->bulkInsert($cacheData);

        if (empty($error))
            Cache::forget('categories');

        return to_route('categories.cache_list');
    }

    /**
     * Clear the specified resource into the cache.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache()
    {
        Cache::forget('categories');
        return to_route('categories.cache_list');
    }

    /**
     * Clear the specified resource into the cache.
     * @param array
     * @return string
     */
    public function bulkInsert($dataArr)
    {
        $convertedData = [];
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($dataArr as $data) {
            $convertedData[] = [
                'name' => $data,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        DB::beginTransaction();
        try {
            Category::insert($convertedData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $error = $e->getMessage();
            return $error;
        }
    }
}
