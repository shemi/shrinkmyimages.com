<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shrinks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();

            $table->string('mode')->default('best');
            $table->bigInteger('max_width')->nullable();
            $table->bigInteger('max_height')->nullable();

            $table->integer('status')->default(0);

            $table->bigInteger('before_total_size')->nullable();
            $table->bigInteger('after_total_size')->nullable();

            $table->timestamp('expire_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shrinks');
    }
}
