<?php

namespace Alpha\App\Models;

class Entry extends Model
{   
    protected $table = 'alpha_entries';

    protected $fillable = [
        'title', 'amount', 'ledger_id', 'note', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'ledger_id' => 'int'
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function($model) {
            $model->attributes = array_filter($model->attributes);
            if (isset($model['created_at'], $model['updated_at'])) {
                $model->timestamps = false;
            }
        });
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
