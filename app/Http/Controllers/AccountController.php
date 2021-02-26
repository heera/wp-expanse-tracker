<?php

namespace Alpha\App\Http\Controllers;

use Alpha\App\Models\Entry;
use Alpha\App\Models\Account;
use Alpha\Framework\Request\Request;
use Alpha\Framework\Validator\ValidationException;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $account = Account::with('ledgers.entries')->latest('created_at');

        if ($search = $request->search) {
            $account->where("name", "LIKE", "%$search%");
        }

        $accounts = $request->all ? $account->get() : $account->paginate(8);
        
        if ($request->all) {
            return [
                'accounts' => $accounts,
                'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
                'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
            ];
        }

        $accounts->each(function($account) {
            $amount = 0.00;
            $account->ledgers->each(function($ledger) use (&$amount) {
                $ledger->entries->each(function($entry) use (&$amount) {
                    $amount = $amount + $entry->amount;
                });
            });
            $account->total = floatVal($amount);
        });

        return [
            'accounts' => $accounts,
            'total' => Entry::sum('amount'),
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function find(Request $request, $id)
    {
        $account = Account::find($id);

        $ledgerQuery = $account->ledgers()->latest();

        if ($search = $request->search) {
            $ledgerQuery->where("name", "LIKE", "%$search%");
        }
        
        $account->ledgers = $ledgerQuery->paginate(8);

        $account->ledgers->each(function($ledger) {
            $amount = 0.00;
            $ledger->entries->each(function($entry) use (&$amount) {
                $amount = $amount + $entry->amount;
            });
            $ledger->total = $amount;
        });


        $ledgerIds = Account::with('ledgers')->find($id)->ledgers->lists('id');
        $entries = Entry::whereIn('ledger_id', $ledgerIds->toArray())->get();
        $account->total = $entries->sum('amount');
        
        return [
            'account' => $account,
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function save(Request $request, $id = null)
    {
        $data = $this->validate(
            $request->only(['name', 'note']),
            ['name' => 'required|unique:alpha_accounts,name,'.$id]
        );

        if ($id) {
            return Account::where('id', $id)->update($data);
        }

        return Account::create($data);
    }

    public function delete($id)
    {
        return Account::where('id', $id)->delete();
    }
}
