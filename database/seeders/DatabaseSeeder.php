<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // remember that seeder must be call in order 
    $this->call([
      UserTableSeeder::class, // call UserTableSeeder and execute UserFactory.php
      PollTableSeeder::class, // call PollTableSeeder and execute PollFactory.php
      PollOptionTableSeeder::class, // call PollOptionTableSeeder and execute PollOptionFactory.php
      VoterTableSeeder::class, // call VoterTableSeeder and execute PollOptionFactory.php
    ]);
  }
}
