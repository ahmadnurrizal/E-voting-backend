<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollOptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('poll_options', function (Blueprint $table) {
      $table->id();
      $table->foreignId('poll_id')->constrained('polls')->onUpdate('cascade')->onDelete('cascade');
      $table->string('option');
      $table->string('image_path')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('poll_options');
  }
}
