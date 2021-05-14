<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMainmenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_mainmenus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('viewname_id');
            $table->string('name');
            $table->string('icon');
            $table->string('link');
            $table->integer('menu_position');
            $table->integer('mainmenu_id');
            $table->integer('mainmenustatus_id');
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
        Schema::dropIfExists('table_mainmenus');
    }
}
