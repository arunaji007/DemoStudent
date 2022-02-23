<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->binary('path');
            $table->integer('type');
            $table->integer('pages')->default(0)->nullable();
            $table->time('duration')->default('00:00:00')->nullable();
            $table->foreignId('chapter_id')->constrained('chapters');
            $table->timestamps();
        });
     #   DB::statement("ALTER TABLE contents AUTO_INCREMENT = 90000 ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'contents',
            function (Blueprint $table) {
                $table->dropForeign(['subject_id']);
            }
        );
    }
}
