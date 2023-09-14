<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class country extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "country";
    protected $primaryKey = 'id_country';

    public function users()
    {
        return $this->belongsToMany(User::class, 'likes', 'id_interest', 'id_user');
    }
}
  