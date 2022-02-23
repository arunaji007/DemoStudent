<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('content');
            $table->boolean('correct');
            $table->foreignId('question_id')->constrained('questions');
            $table->string('solution')->nullable();
            $table->timestamps();
        });
      #  DB::statement("ALTER TABLE answers AUTO_INCREMENT = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'answers',
            function (Blueprint $table) {
                $table->dropForeign(['question_id']);
            }
        );
    }
}
