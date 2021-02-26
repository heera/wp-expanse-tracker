<?php

namespace Alpha\App\Http\Controllers;

use Alpha\App\Models\Entry;
use Alpha\App\Models\Account;
use Alpha\Framework\Request\Request;
use Alpha\Framework\Validator\ValidationException;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = [];
        $totalByAccount = 0;
        
        $entryQuery = Entry::with('ledger.account')->latest();

        if ($search = $request->search) {
            $entryQuery->where("title", "LIKE", "%$search%");
        }

        if ($account = (int) $request->account) {
            $account = Account::with('ledgers')->find($account);
            if ($ledgerIds = $account->ledgers->lists('id')) {
                $entryQuery->whereIn('ledger_id', $ledgerIds);
                $entries = $entryQuery->paginate(9);
                $totalByAccount = Entry::whereIn('ledger_id', $ledgerIds)->sum('amount');
            }
        } else {
            $entries = $entryQuery->paginate(9);
        }

        return [
            'entries' => $entries,
            'total' => Entry::sum('amount'),
            'total_by_account' => $totalByAccount,
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function find($id)
    {
        return [
            'total' => Entry::sum('amount'),
            'entry' => Entry::with('ledger.account')->find($id),
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function save(Request $request, $id = null)
    {
        $data = $this->validate(
            $request->only(['title', 'created_at', 'amount', 'ledger_id', 'note']), [
                'title' => 'required',
                'amount' => 'required|numeric',
                'ledger_id' => 'required'
            ]
        );

        if ($id) {
            return Entry::where('id', $id)->update($data);
        }

        if (isset($data['datetime'])) {
            $data['created_at'] = $data['datetime'];
            $data['updated_at'] = $data['datetime'];
        }

        return Entry::create($data);
    }

    public function delete($id)
    {
        return Entry::where('id', $id)->delete();
    }

    public function entriesByAccount($id)
    {
        $ledgerIds = Account::with('ledgers')->find($id)->ledgers->lists('id');
        $entryQuery = Entry::with('ledger.account')->latest();
        return $entryQuery->whereIn('ledger_id', $ledgerIds)->paginate(8);
    }
}
