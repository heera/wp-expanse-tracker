<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;
use Alpha\App\Models\WP\Traits\HasMeta;
use Alpha\App\Models\WP\Traits\HasRoles;

class User extends Model
{
    use HasMeta, HasRoles;

    protected $table = 'users';
    public $timestamps = false;
    protected $primaryKey = 'ID';

    const CREATED_AT = 'user_registered';

    public function posts()
    {
        return $this->hasMany(Post::class, 'post_author')
                    ->where('post_status', 'publish')
                    ->where('post_type', 'post');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }


    public function meta()
    {
        return $this->hasMany(UserMeta::class, 'user_id')->select([
            'user_id',
            'meta_key',
            'meta_value'
        ]);
    }
}
