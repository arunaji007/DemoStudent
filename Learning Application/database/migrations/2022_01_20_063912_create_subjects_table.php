<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('grade_id')->constrained('grades');
            $table->timestamps();
            # $table->softDeletes();
        });
        DB::statement("ALTER TABLE subjects AUTO_INCREMENT = 10 ;");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'subjects',
            function (Blueprint $table) {
                $table->dropForeign(['grade_id']);
            }
        );
    }
}
