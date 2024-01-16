<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralBonus;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function transaction()
    {
        $transaction = Transaction::with('user')->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        return view('admin.transaction.index', compact('transaction'));
    }

    public function transactionSearch(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $transaction = Transaction::with('user')->orderBy('id', 'DESC')
            ->when(isset($search['transaction_id']), function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(isset($search['user_name']), function ($query) use ($search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search['user_name']}%")
                        ->orWhere('username', 'LIKE', "%{$search['user_name']}%");
                });
            })
            ->when(isset($search['remark']), function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->paginate(config('basic.paginate'));
        $transaction =  $transaction->appends($search);
        return view('admin.transaction.index', compact('transaction'));
    }


    public function commissions()
    {
        $transactions =  ReferralBonus::latest()->with('user','bonusBy:id,firstname,lastname,username,image')->paginate(config('basic.paginate'));
        return view('admin.transaction.commission', compact('transactions'));
    }

    public function commissionsSearch(Request $request)
    {
        $search = $request->all();

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $transactions = ReferralBonus::with('user','bonusBy:id,firstname,lastname,username')
            ->when(isset($search['from_name']), function ($query) use ($search) {
                return $query->whereHas('bonusBy', function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', "%{$search['from_name']}%")
                        ->orWhere('lastname', 'LIKE', "%{$search['from_name']}%")
                        ->orWhere('username', 'LIKE', "%{$search['from_name']}%");
                });
            })
            ->when(isset($search['to_name']), function ($query) use ($search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', "%{$search['to_name']}%")
                        ->orWhere('lastname', 'LIKE', "%{$search['to_name']}%")
                        ->orWhere('username', 'LIKE', "%{$search['to_name']}%");
                });
            })
            ->when(isset($search['remark']), function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when(isset($search['type']), function ($query) use ($search) {
                return $query->where('type', 'LIKE', "%{$search['type']}%");
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'DESC')->paginate(config('basic.paginate'));
        $transactions =  $transactions->appends($search);
        return view('admin.transaction.commission', compact('transactions'));
    }


}
