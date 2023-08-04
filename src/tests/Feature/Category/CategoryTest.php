<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CategoryTest extends TestCase
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
        Category::factory(2)->create();
    }

    /**
     * A dataProvider for store test.
     */
    public function params_for_store()
    {
        return [
            0 => [
                'case' => 'validation error',
                'param' => ["name_0" => ""],
                'errorMessage' => "The name field is required."
            ],
            1 => [
                'case' => 'db entry error',
                'param' => ["name_0" => "a", "name_1" => "a"],
                'errorMessage' => "The name field has a duplicate value."
            ],
            2 => [
                'case' => 'store success',
                'param' => ["name_0" => "a", "name_1" => "b"],
                'errorMessage' => null
            ],
        ];
    }

    /**
     * A basic feature test get categories.
     * @test
     * @return void
     */
    public function get_categories()
    {
        $user = User::first();

        $response = $this->actingAs($user, 'web')
            ->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Category/Index')
                    ->has('categories', 2)
                    ->etc();
            });
    }

    /**
     * A basic feature test store category.
     * @test
     * @dataProvider params_for_store
     * @testdox testCase: $case. errorMessage is $errorMessage, 
     * 
     * @return void
     */
    public function store_categories($case, $param, $errorMessage)
    {
        $user = User::first();

        $response = $this->actingAs($user, 'web')
            ->post(route('categories.store'), $param);

        if ($case === 'validation error') {
            $response->assertStatus(400)
                ->assertInertia(function (AssertableInertia $page) use ($errorMessage) {
                    $page->component('Category/Index')
                        ->has('categories', 2)
                        ->has('errors', 1)
                        ->where('prevRequestData', ['name_0' => null])
                        ->where('errors.0.name.0', $errorMessage)
                        ->etc();
                });
        } else if ($case === 'db entry error') {
            $response->assertStatus(400)
                ->assertInertia(function (AssertableInertia $page) use ($param, $errorMessage) {
                    $page->component('Category/Index')
                        ->has('categories', 2)
                        ->has('errors', 1)
                        ->where('prevRequestData', $param)
                        ->where('errors.0', $errorMessage)
                        ->etc();
                });
        } else if ($case === 'store success') {
            $response->assertStatus(302);

            $this->followingRedirects($response)
                ->get(route('categories.index'))
                ->assertInertia(function (AssertableInertia $page) {
                    $page->component('Category/Index')
                        ->has('categories', 4)
                        ->has('errors', 0)
                        ->etc();
                });
        }
    }

    /**
     * A basic feature test update category.
     * @test
     * @return void
     */
    public function update_category()
    {
        $user = User::first();
        $category = Category::latest('id')->first();

        $dataSets = [
            0 => [
                'case' => 'validation error',
                'id' => $category->id,
                'param' => ["name" => ""],
                'errorMessage' => "The name field is required."
            ],
            1 => [
                'case' => 'duplication error',
                'id' => $category->id,
                'param' => ["name" => $category->name],
                'errorMessage' => "The name has already been taken."
            ],
            2 => [
                'case' => 'update success',
                'id' => $category->id,
                'param' => ["name" => $category->name . 'a'],
                'errorMessage' => null
            ],
        ];

        foreach ($dataSets as $dataSet) {
            $case = $dataSet['case'];
            $param = $dataSet['param'];
            $errorMessage = $dataSet['errorMessage'];

            $response = $this->actingAs($user, 'web')
                ->post(route('categories.store'), $param);

            if ($case === 'validation error') {
                $response->assertStatus(400)
                    ->assertInertia(function (AssertableInertia $page) use ($errorMessage) {
                        $page->component('Category/Index')
                            ->has('categories', 2)
                            ->has('errors', 1)
                            ->where('prevRequestData', ['name' => null])
                            ->where('errors.0.name.0', $errorMessage)
                            ->etc();
                    });
            } else if ($case === 'duplication error') {
                $response->assertStatus(400)
                    ->assertInertia(function (AssertableInertia $page) use ($param, $errorMessage) {
                        $page->component('Category/Index')
                            ->has('categories', 2)
                            ->has('errors', 1)
                            ->where('prevRequestData', $param)
                            ->where('errors.0.name.0', $errorMessage)
                            ->etc();
                    });
            } else if ($case === 'update success') {
                $response->assertStatus(302);

                $this->followingRedirects($response)
                    ->get(route('categories.index'))
                    ->assertInertia(function (AssertableInertia $page) {
                        $page->component('Category/Index')
                            ->has('categories', 3)
                            ->has('errors', 0)
                            ->etc();
                    });
            }
        }
    }

    /**
     * delete category test.
     * @test
     * @return void
     */
    public function delete_category()
    {
        $user = User::first();
        $categories = Category::all();

        $this->assertCount(2, $categories);

        $response = $this->actingAs($user, 'web')
            ->delete(route('categories.destroy', $categories[0]->id));
        $response->assertStatus(302);

        $response = $this->followingRedirects($response)
            ->get(route('categories.index'))
            ->assertInertia(function (AssertableInertia $page) {
                $page->component('Category/Index')
                    ->has('categories', 1)
                    ->etc();
            });
    }
}
