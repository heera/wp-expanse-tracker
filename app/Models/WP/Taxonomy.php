<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class Taxonomy extends Model
{
    protected $table = 'term_taxonomy';

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }
}
