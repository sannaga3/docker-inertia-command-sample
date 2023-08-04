<?php

namespace Tests\Feature\Task;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker, HasFactory, DatabaseTransactions;

    /**
     * before running test, make category.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
        $this->seed('TaskTableSeeder');
        Category::factory(3)->create();
    }

    /**
     * get tasks test.
     * @test
     * @return void
     */
    public function get_tasks()
    {
        $user = User::first();

        $response = $this->actingAs($user, 'web')
            ->get(route('tasks.index'));

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200)
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Task/Index')
                    ->has('tasks', 2)
                    ->etc();
            });

        $tasks = $response['page']['props']['tasks'];
        $this->assertCount(2, $tasks);

        foreach ($tasks as $task) {
            if ($task['id'] === $tasks[0]['id']) {
                $this->assertSame('example title2', $task['title']);
                $this->assertSame('example content2', $task['content']);
                $this->assertSame('2023-07-24', $task['date']);
                $this->assertSame(1, $task['finished']);
                $this->assertSame(0, $task['published']);
                $this->assertSame($user->id, $task['user_id']);
            }
            if ($task['id'] === $tasks[1]['id']) {
                $this->assertSame('example title1', $task['title']);
                $this->assertSame('example content1', $task['content']);
                $this->assertSame('2023-07-10', $task['date']);
                $this->assertSame(0, $task['finished']);
                $this->assertSame(1, $task['published']);
                $this->assertSame($user->id, $task['user_id']);
            }
        }
    }

    /**
     * store tasks test.
     * @test
     * @return void
     */
    public function store_task()
    {
        $taskCount = Task::pluck('id')->count();
        $this->assertSame(2, $taskCount);

        $user = User::first();

        $response = $this->actingAs($user, 'web')
            ->post(route(
                'tasks.store',
                [
                    'title' => 'example title3',
                    'content' => 'example content3',
                    'date' => '2023-07-25',
                    'finished' => 0,
                    'published' => 1,
                    'user_id' => $user->id,
                    'categories' => []
                ]
            ));
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('tasks.index'))
            ->assertInertia(function (AssertableInertia $page) use ($user) {
                $page->component('Task/Index')
                    ->has('tasks', 3)
                    ->where('tasks.0.title', 'example title3')
                    ->where('tasks.0.content', 'example content3')
                    ->where('tasks.0.date', '2023-07-25')
                    ->where('tasks.0.finished', 0)
                    ->where('tasks.0.published', 1)
                    ->where('tasks.0.user_id',  $user->id)
                    ->etc();
            });
    }

    /**
     * store tasks test with taskCategories.
     * @test
     * @return void
     */
    public function store_task_with_task_categories()
    {
        $taskCount = Task::count();
        $categoryIds = Category::pluck('id');
        $this->assertSame(2, $taskCount);

        $user = User::first();
        $response = $this->actingAs($user, 'web')
            ->post(route(
                'tasks.store',
                [
                    'title' => 'example title3',
                    'content' => 'example content3',
                    'date' => '2023-07-25',
                    'finished' => 0,
                    'published' => 1,
                    'user_id' => $user->id,
                    'categoryIds' => [$categoryIds[0], $categoryIds[1]]
                ]
            ));
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('tasks.index'))
            ->assertInertia(function (AssertableInertia $page) use ($categoryIds) {
                $page->component('Task/Index')
                    ->has('tasks', 3)
                    ->where('tasks.0.categories.0.id', $categoryIds[0])
                    ->where('tasks.0.categories.1.id', $categoryIds[1])
                    ->etc();
            });
    }

    /**
     * update tasks test.
     * @test
     * @return void
     */
    public function update_task()
    {
        $user = User::first();
        $task = Task::first();

        $this->assertSame('example title1', $task['title']);
        $this->assertSame('example content1', $task['content']);
        $this->assertSame('2023-07-10', $task['date']);
        $this->assertSame(0, $task['finished']);
        $this->assertSame(1, $task['published']);
        $this->assertSame($user->id, $task['user_id']);

        $response = $this->actingAs($user, 'web')
            ->patch(route('tasks.update', ['task' => $task->id]), [
                'title' => 'updated title',
                'content' => 'updated content',
                'date' => '2023-07-26',
                'finished' => 1,
                'published' => 0,
                'categories' => []
            ]);
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('tasks.index'))
            ->assertInertia(function (AssertableInertia $page) use ($user) {
                $page->component('Task/Index')
                    ->has('tasks', 2)
                    ->where('tasks.0.title', 'updated title')
                    ->where('tasks.0.content', 'updated content')
                    ->where('tasks.0.date', '2023-07-26')
                    ->where('tasks.0.finished', 1)
                    ->where('tasks.0.published', 0)
                    ->where('tasks.0.user_id',  $user->id)
                    ->etc();
            });
    }

    /**
     * update tasks test with taskCategories.
     * @test
     * @return void
     */
    public function update_task_with_task_categories()
    {
        $categoryIds = Category::pluck('id');
        $taskIds = Task::pluck('id');
        $this->assertCount(2, $taskIds);

        $user = User::first();
        $response = $this->actingAs($user, 'web')
            ->patch(route('tasks.update', ['task' => $taskIds[0]]), [
                'title' => 'updated title',
                'content' => 'updated content',
                'date' => '2023-07-26',
                'finished' => 1,
                'published' => 0,
                'categoryIds' => [$categoryIds[0], $categoryIds[1]]
            ]);
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('tasks.index'))
            ->assertInertia(function (AssertableInertia $page) use ($categoryIds) {
                $page->component('Task/Index')
                    ->has('tasks', 2)
                    ->where('tasks.0.categories.0.id', $categoryIds[0])
                    ->where('tasks.0.categories.1.id', $categoryIds[1])
                    ->etc();
            });
    }

    /**
     * delete task test.
     * @test
     * @return void
     */
    public function delete_task()
    {
        $user = User::first();

        $tasks = Task::all();
        $this->assertCount(2, $tasks);

        $task = $tasks[0];

        $response = $this->actingAs($user, 'web')
            ->delete(route('tasks.destroy', ['task' => $task->id]));
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('tasks.index'))
            ->assertInertia(function (AssertableInertia $page) use ($user) {
                $page->component('Task/Index')
                    ->has('tasks', 1)
                    ->where('tasks.0.title', 'example title2')
                    ->where('tasks.0.content', 'example content2')
                    ->where('tasks.0.date', '2023-07-24')
                    ->where('tasks.0.finished', 1)
                    ->where('tasks.0.published', 0)
                    ->where('tasks.0.user_id',  $user->id)
                    ->etc();
            });
    }
}