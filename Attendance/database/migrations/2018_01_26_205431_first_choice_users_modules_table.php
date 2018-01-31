<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FirstChoiceUsersModulesTable extends Migration
{
    /**
     * Run the migrations.
     * Used to determine the main live chat in the main menu
     * @return void
     */
    public function up()
    {
        Schema::create('firstChoiceUserModule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('module_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('first_choice_user_module');
    }
}
