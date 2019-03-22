<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->foreign('id')->references('id')->on('personas')->onDelete('cascade'); //Crea relacion entre la tabla users y personas mediante el campo id
             
            $table->string('usuario')->unique();
            $table->string('password');
            $table->boolean('condicion')->default(1);
 
            $table->integer('idrol')->unsigned();
            $table->foreign('idrol')->references('id')->on('roles'); //Crea relacion entre la tabla users y roles mediante el campo idrol
 
 
            $table->rememberToken();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
