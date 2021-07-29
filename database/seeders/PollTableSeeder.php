<?php

namespace Database\Seeders;

use App\Models\PollOption;
use App\Models\Poll;
use Illuminate\Database\Seeder;

class PollTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\Poll::factory()->count(20)->create(); // generate poll table 20 records

    /* Has Many Relationships

        Poll::factory()
        ->has(PollOption::factory()->count(3)) // create 3 pollOption records each poll record
        ->count(20)->create(); // create 20 poll records
    */

    // Using Magic Methods
    Poll::factory()
      ->hasPollOptions(3) // create 3 pollOption records each poll record
      ->count(100)->create(); // create 100 poll records
  }
}
