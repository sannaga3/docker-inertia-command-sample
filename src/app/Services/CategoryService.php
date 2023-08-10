<?php

namespace App\Services;

use App\Models\Category;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class CategoryService
{
  /**
   * Display a listing of the resource.
   * 
   * @return array
   */
  public function getCategories()
  {
    $updatedId = Session::get('updatedId') ? intval(Session::get('updatedId')) : null;
    $categories = Category::orderBy('id', 'desc')
      ->whereNull('deleted_at')
      ->get();

    if ($updatedId)
      return ['categories' => $categories, 'updatedId' => $updatedId];

    return ['categories' => $categories];
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param array $names
   * @return array|string $errors
   */
  public function storeCategories($names)
  {
    $errors = [];
    foreach ($names as $name) {
      $validator = $this->validateName($name);

      if ($validator->fails()) {
        $errors[] = $validator->errors();
      }
    }

    if (!empty($errors)) {
      return $errors;
    }

    $errorMessage = $this->bulkInsert($names);

    if (isset($errorMessage)) {
      if (false !== strpos($errorMessage, 'Duplicate entry'))
        $errors[] = ['name' => ["The name field has a duplicate value."]];
      else
        $errors[] = ['name' => ["The name field is required."]];

      return $errors;
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param App\Models\Category $category
   * @return \Illuminate\Support\MessageBag $errors
   */
  public function updateCategory(Request $request, Category $category)
  {
    try {
      $validator = $this->validateName($request->name);
      $category->update(['name' => $request->name]);
    } catch (Exception) {
      $errors = $validator->errors();

      return $errors;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Category $category
   */
  public function deleteCategory(Category $category)
  {
    DB::beginTransaction();
    $taskCategories = TaskCategory::where('category_id', $category->id)
      ->whereNull('deleted_at')
      ->get();
    TaskCategory::destroy($taskCategories);
    DB::commit();

    $category->delete();
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
