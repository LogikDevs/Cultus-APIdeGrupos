<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\post;

class PostSeeder extends Seeder
{

    public function run()
    {      
        \App\Models\post::factory(10)->create();
    }
}
