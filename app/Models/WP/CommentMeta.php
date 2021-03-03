<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class CommentMeta extends Model
{
    public $timestamps = false;
    protected $table = 'commentmeta';
    protected $primaryKey = 'meta_id';
    protected $fillable = ['meta_key', 'meta_value'];


    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
