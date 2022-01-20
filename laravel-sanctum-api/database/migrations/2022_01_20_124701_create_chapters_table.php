<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('subjectId')->constrained('subjects');
            $table->timestamps();
            # $table->softDeletes();
        });
        DB::statement("ALTER TABLE chapters AUTO_INCREMENT = 1000 ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'chapters',
            function (Blueprint $table) {
                $table->dropForeign(['subjectId']);
            }
        );
    }
}
