<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('category_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
			$table->string('title');
			$table->string('platformID')->unique();
			$table->enum('paid', [0,1])->default(0);
			$table->double('amount')->default(0);
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->enum('location', [0,1])->default(0);
			$table->enum('status', [1,2,3])->default(1);
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
        Schema::dropIfExists('streams');
    }
}
