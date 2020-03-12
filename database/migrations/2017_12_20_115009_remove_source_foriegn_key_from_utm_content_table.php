<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSourceForiegnKeyFromUtmContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utm_contents', function (Blueprint $table) {
            // $table->dropColumn(['source_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utm_contents', function (Blueprint $table) {
            // $table->unsignedInteger('source_id')->after('user_id');
        });
    }
}
