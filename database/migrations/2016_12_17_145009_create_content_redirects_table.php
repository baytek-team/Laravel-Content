<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_redirect', function (Blueprint $table) {
            $table->increments('id');
            // $table->timestamp('created_at');
            // $table->timestamp('deleted_at');
            $table->integer('status_code')->unsigned()->nullable();
            $table->string('type')->nullable();
            $table->text('location')->nullable();
            $table->text('redirects_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_history');
    }
}
