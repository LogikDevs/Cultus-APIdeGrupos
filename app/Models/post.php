<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{   
    use SoftDeletes;
    use HasFactory;
    protected $table = "post";
    protected $primaryKey = 'id_post';

    protected $fillable = [
        'text',
        'latitud',
        'longitud',
        'comments',
        'votes'
    ];
}

