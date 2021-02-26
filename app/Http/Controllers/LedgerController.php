<?php

namespace Alpha\App\Http\Controllers;

use Alpha\App\Models\Entry;
use Alpha\App\Models\Ledger;
use Alpha\App\Models\Account;
use Alpha\Framework\Request\Request;
use Alpha\Framework\Validator\ValidationException;

class LedgerController extends Controller
{

    public function index(Request $request)
    {
        if ($accountId = $request->account_id) {
            return Ledger::where('account_id', $accountId)->latest()->get();
        }

        $ledger = Ledger::with('account')->latest();

        if ($search = $request->search) {
            $ledger->where("name", "LIKE", "%$search%");
        }
        
        $ledgers = $ledger->paginate(8);

        $ledgers->each(function($ledger) {
            $amount = 0.00;
            $ledger->entries->each(function($entry) use (&$amount) {
                $amount = $amount + $entry->amount;
            });
            $ledger->total = $amount;
        });

        return [
            'total' => Entry::sum('amount'),
            'accounts' => Account::all(),
            'ledgers' => $ledgers,
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function find(Request $request, $id)
    {
        $ledger = Ledger::find($id);
        $entryQuery = $ledger->entries()->latest();
        
        if ($search = $request->search) {
            $entryQuery->where("title", "LIKE", "%$search%");
        }

        $ledger->entries = $entryQuery->paginate(8);

        $entries = Entry::where('ledger_id', $id)->get();
        $ledger->total = $entries->sum('amount');

        return [
            'ledger' => $ledger,
            'total' => Entry::sum('amount'),
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function save(Request $request, $id = null)
    {
        $data = $this->validate(
            $inputs = $request->only(['name', 'account_id', 'note']),
            [
                'account_id' => 'required',
                'name' => 'required|unique:alpha_ledgers,name,'.$id.',id,account_id,'.$inputs['account_id']
            ]
        );

        if ($id) {
            return Ledger::where('id', $id)->update($data);
        }

        return Ledger::create($data);
    }

    public function delete($id)
    {
        return Ledger::where('id', $id)->delete();
    }
}
