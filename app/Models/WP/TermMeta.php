<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class TermMeta extends Model
{
    protected $table = 'term_meta';
    protected $primaryKey = 'meta_id';
    protected $fillable = ['meta_key', 'meta_value'];
}
