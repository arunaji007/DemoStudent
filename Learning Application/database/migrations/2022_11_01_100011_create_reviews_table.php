<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('content_id')->constrained('contents');
            $table->string('notes')->nullable();
            $table->integer('like')->nullable();
            $table->time('lastWatched')->nullable();
            $table->integer('lastRead')->nullable();
            $table->timestamps();
        });
     #   DB::statement("ALTER TABLE reviews AUTO_INCREMENT = 80000 ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(
            'reviews',
            function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['content_id']);
            }
        );
    }
}
