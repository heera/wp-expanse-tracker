<?php

namespace Alpha\App\Models;

use Alpha\Framework\Database\Orm\Model as BaseModel;

class Model extends BaseModel
{
    protected $guarded = ['id', 'ID'];
}