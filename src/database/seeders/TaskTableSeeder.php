<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Factory;
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
        'title' => '筋トレ',
        'content' => '毎日何かしらやる',
        'date' => '2023-07-10',
        'finished' => false,
        'published' => true,
        'user_id' => User::first()->id,
      ],
    ]);
  }
}