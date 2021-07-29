<?php

namespace Database\Factories;

use App\Models\Voter;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoterFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Voter::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $faker = \Faker\Factory::create('id_ID'); // more information : https://github.com/fzaninotto/Faker
    return [
      'user_id' => $faker->numberBetween(1, User::count()),
      'poll_id' => $faker->unique()->randomNumber(2, true),
      'poll_option_id' => rand(1, 3) // Poll::count() ==> return poll count of Poll Table
    ];
  }
}
