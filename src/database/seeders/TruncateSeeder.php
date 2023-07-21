<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TruncateSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    Schema::disableForeignKeyConstraints();
    User::truncate();
    Task::truncate();
    Schema::enableForeignKeyConstraints();
  }
}