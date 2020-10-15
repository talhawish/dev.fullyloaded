<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFbIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->string('fb_id')->after('email')->unique()->nullable();
			$table->string('twitter_id')->after('fb_id')->unique()->nullable();
			$table->string('cover_photo')->after('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->dropColumn('fb_id');
			$table->dropColumn('twitter_id');
			$table->dropColumn('cover_photo');
        });
    }
}
