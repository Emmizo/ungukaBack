<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCropfarmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cropfarms', function (Blueprint $table) {
            $table->Increments('id');
            $table->integer('farmID')->unsigned();
            $table->foreign('farmID')->references('id')->on('farms')->onUpdate('cascade');
            $table->integer('seasonID')->unsigned();
            $table->foreign('seasonID')->references('id')->on('seasons')->onUpdate('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->integer('cropsID')->unsigned();
            $table->foreign('cropsID')->references('id')->on('crops')->onUpdate('cascade');
            $table->integer('status');
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
        Schema::dropIfExists('cropfarms');
    }
}
