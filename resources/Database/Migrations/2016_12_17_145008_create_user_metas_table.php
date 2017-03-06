<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_metas', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('status')->unsigned()->nullable()->default(0)->index();

            $table->string('language')->nullable()->index();
            $table->string('key')->nullable()->index();
            $table->text('value')->nullable();

            $table->index(['user_id', 'key']);
            $table->index(['user_id', 'key', 'status']);
            $table->index(['user_id', 'key', 'language']);
            $table->index(['user_id', 'key', 'language', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_metas');
    }
}
