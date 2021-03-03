<?php

namespace Alpha\App\Http\Controllers;

use DateTime;
use DateInterval;
use Alpha\App\Models\Entry;
use Alpha\App\Models\Account;
use Alpha\Framework\Support\Arr;
use Alpha\Framework\Request\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // return \Alpha\App\Models\WP\User::with('posts.comments', 'meta')->get();
        // $db = \Alpha\App\App::make('db');
        // dd($db->table('posts')->paginate(2)->toArray());
        // dd($this->app->rest->getRoutes());
        // return Account::with('ledgers.entries')->latest('updated_at')->paginate(3);
        
        return [
            'total' => Entry::sum('amount'),
            'accounts' => Account::with('ledgers.entries')->latest('updated_at')->paginate(3),
            'first' => Entry::oldest()->limit(1)->first()->created_at->format('d-m-Y'),
            'last' => Entry::latest()->limit(1)->first()->created_at->format('d-m-Y')
        ];
    }

    public function getChartData(Request $request)
    {
        $inputs = $request->except(['nonce']);
        $inputs = $this->fixDateBetween($inputs);

        $chartData = Entry::where('created_at', '>=', $inputs['from'])
            ->where('created_at', '<=', $inputs['to'])
            ->oldest()
            ->get();

            $stats = [];
            foreach($chartData as $entry) {
                $date = explode(' ', $entry->created_at->format('Y-m-d'));
                $existing = Arr::get($stats, $date[0], 0);
                $stats[$date[0]] = $existing + $entry->amount;
            }

        return [
            'stats' => [
                'labels' => array_keys($stats),
                'values' => array_values($stats)
            ]
        ];
    }

    protected function fixDateBetween($inputs)
    {
        $date = new DateTime($inputs['to']);
        $date->add(new DateInterval('P1D'));
        $date->sub(new DateInterval('PT1S'));
        $inputs['to'] = $date->format('Y-m-d H:i:s');
        
        $inputs['from'] = (new DateTime($inputs['from']))->format('Y-m-d H:i:s');
        
        return $inputs;
    }
}
