<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegratesTable extends Migration
{

    public function up()
    {
        Schema::create('integrates', function (Blueprint $table) {
            
            $table->id('id_integrates');
            $table->unsignedBigInteger("id_user");
            $table->foreign("id_user")->references("id")->on("users");
            $table->unsignedBigInteger("id_group");
            $table->foreign("id_group")->references("id_group")->on("groups");             
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('integrates');
    }
}
