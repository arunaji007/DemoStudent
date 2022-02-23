<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile_no')->unique();
            $table->date('dob');
            $table->timestamps();
            $table->foreignId('board_id')->nullable()->constrained('boards');
            $table->foreignId('grade_id')->nullable()->constrained('grades');
        });
       # DB::statement("ALTER TABLE users AUTO_INCREMENT = 10000;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users', function (Blueprint $table) {
            $table->dropForeign(['board_id']);
            $table->dropForeign(['grade_id']);
        });
    }
}
