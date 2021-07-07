<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('polls', function (Blueprint $table) {
      $table->id();
      $table->integer('user_id');
      $table->string('title');
      $table->string('description');
      $table->string('deadline');
      $table->string('status');
      $table->timestamps(); // automaticaly add colomn created_at & updated_at
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('polls');
  }
}
