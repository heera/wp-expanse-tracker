<?php

namespace Alpha\App\Models\WP;

class Attachment extends Post
{
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_parent', 'ID');
    }
}
