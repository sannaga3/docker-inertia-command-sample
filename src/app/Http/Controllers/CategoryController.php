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
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

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

        $convertedRequest = [];
        foreach ($names as $name) {
            $convertedRequest[] = [
                'name' => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        DB::beginTransaction();
        try {
            Category::insert($convertedRequest);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            $categories = Category::orderBy('id', 'desc')->get();
            $error = $e->getMessage();

            $errors = '';
            if (false !== strpos($error, 'Duplicate entry'))
                $errors = ["The name field has a duplicate value."];
            else
                $errors = ["The name field is required."];

            return Inertia::render('Category/Index', ['categories' => $categories, 'errors' => $errors, 'prevRequestData' => $request])
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
}
