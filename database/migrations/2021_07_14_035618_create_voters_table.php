<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('voters', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('poll_id')->constrained('polls')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('poll_option_id')->constrained('poll_options')->onUpdate('cascade')->onDelete('cascade');
      // $table->unsignedBigInteger('poll_option_id'); // this one
      // $table->foreignId('poll_option_id')->nullable()->constrained('poll_options')->onUpdate('cascade')->onDelete('cascade');
      // $table->foreign('poll_option_id')->references('id')->on('poll_options')->nullable();
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
    Schema::dropIfExists('voters');
  }
}
