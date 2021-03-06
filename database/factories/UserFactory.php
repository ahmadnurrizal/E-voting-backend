<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = User::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $faker = \Faker\Factory::create('id_ID'); // more information : https://github.com/fzaninotto/Faker
    $gender = $faker->randomElement(['Male', 'Female']);
    return [
      'name' => $faker->name,
      'username' => $faker->userName,
      'email' => $faker->unique()->safeEmail,
      'email_verified_at' => now(),
      'password' => bcrypt("123456789"), // convert to hash
      'remember_token' => Str::random(10),
      'date_of_birth' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
      'address' => $faker->address,
      'gender' => $gender,
      'status' => $faker->jobTitle
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  public function unverified()
  {
    return $this->state(function (array $attributes) {
      return [
        'email_verified_at' => null,
      ];
    });
  }
}
