<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            // $table->integer('parent_id')->unsigned();
            // $table->foreign('parent_id')->references('id')->on('contents');

            $table->integer('status')->unsigned()->nullable()->default(0);
            $table->integer('revision')->unsigned()->nullable()->default(0);

            $table->string('language')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
