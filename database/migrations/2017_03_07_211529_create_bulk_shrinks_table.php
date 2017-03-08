<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulkShrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_shrinks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shrink_id');
            $table->unsignedInteger('call_id');

            $table->integer('status')->default(0);

            $table->text('security_token')->nullable();
            $table->text('callback_url')->nullable();
            $table->text('download_url')->nullable();
            $table->text('extra_fields')->nullable();
            $table->text('images')->nullable();
            $table->string('last_image')->nullable();

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
        Schema::dropIfExists('bulk_shrinks');
    }
}
