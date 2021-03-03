<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class PostMeta extends Model
{
    public $timestamps = false;
    protected $table = 'postmeta';
    protected $primaryKey = 'meta_id';
    protected $fillable = ['meta_key', 'meta_value'];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
