<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('boardId')->constrained('boards');
            $table->timestamps();
            #$table->softDeletes();
        });
        DB::statement("ALTER TABLE grades AUTO_INCREMENT = 900;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'grades',
            function (Blueprint $table) {
                $table->dropForeign(['boardId']);
            }
        );
    }
}
