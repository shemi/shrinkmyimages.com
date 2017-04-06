<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixBulkShrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_shrinks', function (Blueprint $table) {
            $table->dropColumn(['security_token', 'last_image', 'images']);

            $table->renameColumn('download_url', 'base_url');
            $table->string('security_type')->nullable();
            $table->text('security_fields')->nullable();

            $table->index(['status', 'call_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_shrinks', function (Blueprint $table) {
            $table->string('security_token');
            $table->string('last_image');
            $table->text('images');
            $table->renameColumn('base_url', 'download_url');
            $table->dropColumn(['security_type', 'security_fields']);
            $table->dropIndex(['status', 'call_id']);
        });
    }
}
