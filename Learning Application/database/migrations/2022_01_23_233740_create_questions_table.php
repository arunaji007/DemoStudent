<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('content');
            $table->integer('type');
            $table->integer('maxMark');
            $table->foreignId('exercise_id')->constrained('exercises');
            $table->timestamps();
        });
      #  DB::statement("ALTER TABLE questions AUTO_INCREMENT = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions', function (Blueprint $table) {
            $table->dropForeign(['exercise_id']);
        });
    }
}
