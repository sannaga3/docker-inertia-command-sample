<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('tasks')->insert([
      [
        'title' => 'example title1',
        'content' => 'example content1',
        'date' => '2023-07-10',
        'finished' => false,
        'published' => true,
        'user_id' => User::first()->id,
      ],
      [
        'title' => 'example title2',
        'content' => 'example content2',
        'date' => '2023-07-24',
        'finished' => true,
        'published' => false,
        'user_id' => User::first()->id,
      ],
    ]);
  }
}