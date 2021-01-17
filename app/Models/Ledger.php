<?php

namespace Alpha\App\Models;

class Ledger extends Model
{   
    protected $table = 'alpha_ledgers';

    protected $fillable = ['name', 'account_id', 'note'];
    
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
