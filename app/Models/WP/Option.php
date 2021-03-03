<?php

namespace Alpha\App\Models\WP;

use Alpha\App\Models\Model;

class Option extends Model
{
    public $timestamps = false;
    protected $table = 'options';
    protected $primaryKey = 'option_id';

    public static function getValue($key = '')
    {
        $value = '';

        if ($key) {
            $value = static::where('option_name', $key)->value('option_value');
        }

        $value = maybe_unserialize($value);

        return $value;
    }
}
