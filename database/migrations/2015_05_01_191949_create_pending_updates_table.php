<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendingUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pending_updates', function (Blueprint $table) {
            $table->string('token');
            $table->string('model');
            $table->integer('id');
            $table->string('update');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->primary('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('pending_updates');
    }
}
