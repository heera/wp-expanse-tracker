<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;
Use Alpha\App\Models\WP\Traits\HasMeta;

class Term extends Model
{
    use HasMeta;

    protected $table = 'terms';
    protected $primaryKey = 'term_id';

    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id')->select([
            'term_id', 'meta_key', 'meta_value'
        ]);
    }
}
