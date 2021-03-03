<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;
use Alpha\App\Models\WP\Traits\HasMeta;

class Comment extends Model  {

    use HasMeta;

    public $timestamps = false;
    protected $table = 'comments';
    protected $primaryKey = 'comment_ID';

    const CREATED_AT = 'comment_date';

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function meta()
    {
        return $this->hasMany(CommentMeta::class, 'comment_id')
                    ->select(['comment_id', 'meta_key', 'meta_value']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'user_id');
    }
}
