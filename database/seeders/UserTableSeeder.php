<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \App\Models\User::factory()->count(310)->create(); // generate user table 10 data
  }
}
