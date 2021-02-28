<?php

namespace Alpha\App\Http\Controllers;

use DateTime;
use DateInterval;
use Alpha\App\Models\Entry;
use Alpha\App\Models\Ledger;
use Alpha\App\Models\Account;
use Alpha\Framework\Request\Request;
use Alpha\Framework\Validator\ValidationException;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $inputs = $request->except(['nonce']);
        
        if ($inputs['date_range'][0] != $inputs['date_range'][1]) {
            $inputs = $this->fixDateBetween($inputs);
        } else {
            $inputs['date_range'][0] = $inputs['date_range'][0] . ' 00-00-00';
            $inputs['date_range'][1] = $inputs['date_range'][1] . ' 23-59-59';
        }
        
        $entry = Entry::with('ledger.account')->whereBetween(
            'created_at', $inputs['date_range']
        )->oldest();

        if (!empty($inputs['ledger_id'])) {
            $entry->where('ledger_id', $inputs['ledger_id']);
        }

        if (empty($inputs['ledger_id']) && !empty($inputs['account_id'])) {
            $ledgerIds = Account::find($inputs['account_id'])->ledgers()->lists('id')->toArray();
            $entry->whereIn('ledger_id', $ledgerIds);
        }

        return [
            'total' => $entry->sum('amount'),
            'entries' => $entry->latest()->paginate(8)
        ];
    }

    protected function fixDateBetween($inputs)
    {
        $date = new DateTime($inputs['date_range'][1]);
        $date->add(new DateInterval('P1D'));
        $date->sub(new DateInterval('PT1S'));
        $inputs['date_range'][1] = $date->format('Y-m-d H:i:s');
        
        $inputs['date_range'][0] = (
            new DateTime($inputs['date_range'][0])
        )->format('Y-m-d H:i:s');

        return $inputs;
    }
}
