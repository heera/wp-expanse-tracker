<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class Link extends Model
{
    protected $table = 'links';
    public $timestamps = false;
    protected $primaryKey = 'link_id';
}
