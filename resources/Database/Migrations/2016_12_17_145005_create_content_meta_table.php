<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_meta', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('content_id')->unsigned()->nullable();
            $table->foreign('content_id')->references('id')->on('contents');

            $table->integer('status')->unsigned()->nullable()->default(0)->index();

            $table->string('language')->nullable()->index();
            $table->string('key')->nullable()->index();
            $table->text('value')->nullable();

            $table->index(['content_id', 'key']);
            $table->index(['content_id', 'key', 'status']);
            $table->index(['content_id', 'key', 'language']);
            $table->index(['content_id', 'key', 'language', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_meta');
    }
}
