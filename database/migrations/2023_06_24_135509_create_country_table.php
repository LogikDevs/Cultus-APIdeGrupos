<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
 
    public function up()
    {
        Schema::create('country', function (Blueprint $table) {
            $table->id("id_country");
            $table->string("country_name");
            $table->timestamps();
            $table->softDeletes();
        });
    }

  
    public function down()
    {
        Schema::dropIfExists('country');
    }
}
