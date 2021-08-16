<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Poll::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $faker = \Faker\Factory::create('id_ID'); // more information : https://github.com/fzaninotto/Faker
    $status = $faker->randomElement(['public', 'private']);
    return [
      'title' => $faker->company,
      'description' => $faker->sentence($nbWords = 9, $variableNbWords = true),
      'deadline' => $faker->date($format = 'Y-m-d', $max = 'now'),
      'status' => $status,
      'user_id' => rand(1, User::count()) // User::count() ==> return User count of User Table
    ];
  }
}
