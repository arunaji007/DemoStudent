<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('score');
            $table->time('duration')->defualt('00:00:00');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('exercise_id')->constrained('exercises');
            $table->softDeletes();
            $table->timestamps();
        });
       # DB::statement("ALTER TABLE attempts AUTO_INCREMENT = 1500;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('attempts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['exercise_id']);
        });
    }
}
