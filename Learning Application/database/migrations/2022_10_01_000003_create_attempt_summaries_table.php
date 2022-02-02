<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attempt_summaries', function (Blueprint $table) {
            $table->integer('answer_type');
            $table->string('answer')->nulllbale();
            $table->integer('mark')->default(-1);
            $table->primary(['attempt_id', 'question_id']);
            $table->foreignId('attempt_id')->constrained('attempts')->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained('questions');
            $table->foreignId('answer_id')->nullbale()->constrained('answers');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attempt_summaries', function (Blueprint $table) {
            $table->dropForeign(['answer_id']);
        });
    }
}
