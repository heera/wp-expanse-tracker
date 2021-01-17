<?php

namespace Alpha\App\Models;

class Account extends Model
{   
    protected $table = 'alpha_accounts';

    protected $fillable = ['name', 'note'];

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
}
