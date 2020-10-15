<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('category_id')->unsigned();
			$table->foreign('category_id')->references('id')->on('categories');
			$table->string('title')->nullable();
			$table->string('starts_at')->nullable();
			$table->string('ends_at')->nullable();
			$table->string('attachment')->nullable();
			$table->enum('paid', [0,1])->default(0);
			$table->enum('status', [0,1])->default(1);
			$table->enum('limited', [0,1])->default(1);
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
        Schema::dropIfExists('events');
    }
}
