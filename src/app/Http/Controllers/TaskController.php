<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $users = User::select('id', 'name');

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
            ->joinSub($users, 'users', function ($join) {
                $join->on('users.id', '=', 'tasks.user_id');
            })
            ->orderBy('tasks.date', 'desc')
            ->get();

        return Inertia::render('Task/Index', ['tasks' => $tasks]);
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

        Task::create($request->all());

        return redirect()->route('tasks.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $task->update($request->all());

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
        $task->delete();
        return redirect()->route('tasks.index');
    }
}