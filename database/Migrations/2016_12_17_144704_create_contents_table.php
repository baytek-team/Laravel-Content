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

            $table->integer('status')->unsigned()->nullable()->default(0)->index();
            $table->integer('revision')->unsigned()->nullable()->default(0)->index();

            $table->string('language')->nullable();
            $table->string('key');//->index();
            $table->string('title')->nullable();
            $table->text('content')->nullable();

            // $table->index(['status', 'key']);
            // $table->index(['revision', 'language', 'key']);
            // $table->index(['revision', 'language', 'key', 'status']);
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
