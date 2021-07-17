<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->Increments('id');
            $table->integer('farmID')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('cropfarmID')->unsigned()->nullable();
            $table->string('description');
            $table->string('moneySpent');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('farmID')->references('id')->on('farms')->onUpdate('cascade');    
            $table->foreign('cropfarmID')->references('id')->on('cropfarms')->onUpdate('cascade');    
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
        Schema::dropIfExists('expenses');
    }
}
