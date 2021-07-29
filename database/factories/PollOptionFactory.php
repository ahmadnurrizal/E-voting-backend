<?php

namespace Database\Factories;

use App\Models\PollOption;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollOptionFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = PollOption::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $faker = \Faker\Factory::create('id_ID'); // more information : https://github.com/fzaninotto/Faker
    $status = $faker->randomElement(['Opened', 'Closed']);
    return [
      'option' => $faker->sentence($nbWords = 5, $variableNbWords = true),
      'image_path' => 'Image_path blablabla',
      'poll_id' => rand(1, Poll::count()) // Poll::count() ==> return poll count of Poll Table
    ];
  }
}
