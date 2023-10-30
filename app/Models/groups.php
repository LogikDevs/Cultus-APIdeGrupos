<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class groups extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "groups";
    protected $primaryKey = 'id_group';

    public function id_chat(): BelongsTo
    {
        return $this->belongsTo(chat::class, 'id_chat');
    }
}
