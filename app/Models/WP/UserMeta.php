<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class UserMeta extends Model
{
    public $timestamps = false;
    protected $table = 'usermeta';
    protected $primaryKey = 'umeta_id';
    protected $fillable = ['meta_key', 'meta_value'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
