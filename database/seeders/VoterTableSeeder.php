<?php

namespace Database\Seeders;

use App\Models\Voter;
use Illuminate\Database\Seeder;

class VoterTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Voter::factory()->count(500)->create(); // generate voter table 100 records
  }
}
