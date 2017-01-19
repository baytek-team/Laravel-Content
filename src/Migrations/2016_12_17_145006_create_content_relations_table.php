<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_relations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('content_id')->unsigned()->nullable();
            $table->foreign('content_id')->references('id')->on('contents');

            $table->integer('relation_id')->unsigned()->nullable();
            $table->foreign('relation_id')->references('id')->on('contents');

            $table->integer('relation_type_id')->unsigned()->nullable();
            $table->foreign('relation_type_id')->references('id')->on('contents');
            // or
            // $table->string('relation_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_relations');
    }
}
