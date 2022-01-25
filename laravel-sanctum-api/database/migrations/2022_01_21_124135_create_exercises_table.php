<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->time('duration');
            $table->integer('noOfQuestions');
            $table->foreignId('chapter_id')->constrained('chapters');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE exercises AUTO_INCREMENT = 750 ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'exercises',
            function (Blueprint $table) {
                $table->dropForeign(['chapter_id']);
            }
        );
    }
}
