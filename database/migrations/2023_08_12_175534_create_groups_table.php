<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{

    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id("id_group");
            $table->string("name");
            $table->string("description")->nullable();
            $table->string("picture")->nullable();
            $table->string("privacy")->default('Public');
           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
