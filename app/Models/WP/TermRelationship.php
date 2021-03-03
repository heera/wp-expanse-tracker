<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class TermRelationship extends Model
{
    protected $table = 'term_relationships';
    protected $primaryKey = 'term_taxonomy_id';
}
