<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class integrates extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'id_integrates';
    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class, 'id_user');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(groups::class, 'id_group', 'id_group');
    }
}
