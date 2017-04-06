<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulkShrinkImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_shrink_images', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bulk_shrink_id');
            $table->unsignedInteger('file_id')->nullable();

            $table->string('url');
            $table->text('data')->nullable();
            $table->integer('status')->default(0);
            $table->string('error_message')->nullable();

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
        Schema::dropIfExists('bulk_shrink_images');
    }
}
