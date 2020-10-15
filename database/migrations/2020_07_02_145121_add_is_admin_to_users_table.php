<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAdminToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('balance',8,3)->after('email')->default(0);
            $table->string('address')->after('balance')->nullable();
            $table->double('latitude',10,7)->after('address')->nullable();
            $table->double('longitude',10,7)->after('latitude')->nullable();
            $table->string('photo')->after('longitude')->nullable();
            $table->tinyInteger('is_admin')->after('photo')->default(0);
            $table->string('fcm_token')->after('is_admin')->nullable();
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
            $table->dropColumn('balance');
            $table->dropColumn('address');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('photo');
            $table->dropColumn('is_admin');
            $table->dropColumn('fcm_token');
            
        });
    }
}
