<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use App\Models\TaskCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::selectRaw('
                tasks.id,
                users.name AS username,
                tasks.user_id,
                tasks.title,
                tasks.content,
                tasks.date,
                tasks.finished,
                tasks.published
            ')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'tasks.user_id');
            })
            ->whereNull('tasks.deleted_at')
            ->with('categories:id,name')
            ->orderBy('tasks.date', 'desc')
            ->orderBy('tasks.id', 'desc')
            ->get();

        $categories = Category::select(['id', 'name'])
            ->orderBy('id', 'desc')
            ->whereNull('deleted_at')
            ->get();

        return Inertia::render('Task/Index', ['tasks' => $tasks, 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Task\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $user = Auth::user();
        $request->merge(['user_id' => $user->id]);

        DB::beginTransaction();
        try {
            $task = Task::create($request->all());

            $categoryIds = $request->categoryIds;
            if (isset($categoryIds) && count($categoryIds) > 0) {
                $timestamp = Carbon::now()->format('Y-m-d H:i:s');
                $taskCategoryParams = [];
                foreach ($categoryIds as $id) {
                    $taskCategoryParams[] =
                        ['task_id' => $task->id, 'category_id' => $id, 'created_at' => $timestamp, 'updated_at' => $timestamp];
                }
                TaskCategory::insert($taskCategoryParams);

                DB::commit();
            }
        } catch (Exception $e) {
            Log::info('error occurred');
            DB::rollback();
        }

        return redirect()->route('tasks.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Task\UpdateTaskRequest $request
     * @param  Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $categoryIds = $request->categoryIds;
        $storedTaskCategories = TaskCategory::select('id', 'category_id', 'deleted_at')
            ->where('task_id', $task->id)
            ->withTrashed()
            ->get();
        $storedCategoryIds = [];
        if (isset($categoryIds)) {
            foreach ($storedTaskCategories as $storedCategory) {
                $storedCategoryIds[] = $storedCategory['category_id'];
            }
        }

        $insertParams = [];
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        if (isset($categoryIds)) {
            foreach ($categoryIds as $id) {
                if (!in_array($id, $storedCategoryIds, true))
                    $insertParams[] = ['task_id' => $task->id, 'category_id' => $id, 'created_at' => $timestamp, 'updated_at' => $timestamp];
            }
        }

        DB::beginTransaction();
        try {
            $deleteIds = [];
            if (isset($catestoredCategoryIdsgoryIds)) {
                foreach ($storedCategoryIds as $i => $storedId) {
                    if (in_array($storedId, $categoryIds, true)) {
                        if (!empty($storedTaskCategories[$i]->deleted_at))
                            $storedTaskCategories[$i]->restore();
                    } else {
                        $deleteIds[] = $storedTaskCategories[$i]->id;
                    }
                }
            }

            $task->update($request->all());
            TaskCategory::insert($insertParams);
            TaskCategory::destroy($deleteIds);

            DB::commit();
        } catch (Exception $e) {
            Log::info('error occurred');
            DB::rollback();
        }

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        DB::beginTransaction();
        $taskCategories = TaskCategory::where('task_id', $task->id)
            ->whereNull('deleted_at')
            ->get();
        TaskCategory::destroy($taskCategories);
        DB::commit();

        $task->delete();
        return redirect()->route('tasks.index');
    }
}
