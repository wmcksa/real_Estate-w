<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Models\Fund;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Stevebauman\Purify\Facades\Purify;

class PaymentLogController extends Controller
{
    use Notify;

    public function index()
    {
        $page_title = "Payment Logs";
        $funds = Fund::where('status', '!=', 0)->where('plan_id', null)->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
        return view('admin.payment.logs', compact('funds', 'page_title'));
    }

    public function pending()
    {
        $page_title = "Payment Pending";
        $funds = Fund::where('status', 2)->where('gateway_id','>', 999)->orderBy('id', 'DESC')->with('user', 'gateway')->paginate(config('basic.paginate'));
        return view('admin.payment.logs', compact('funds', 'page_title'));
    }


    public function search(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $funds = Fund::with('user', 'gateway')
        ->when(isset($search['user']), function ($query) use ($search) {
            return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%{$search['user']}%")
                      ->orWhere('firstname', 'LIKE', "%{$search['user']}%")
                      ->orWhere('lastname', 'LIKE', "%{$search['user']}%")
                      ->orWhere('username', 'LIKE', "%{$search['user']}%");
                });
        })
        ->when(isset($search['trx_id']), function ($query) use ($search) {
            return $query->where('transaction',  'LIKE', "%{$search['trx_id']}%");
        })
        ->when(isset($search['status']), function ($query) use ($search) {
            return $query->where('status', $search['status']);
        })
        ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
            return $q2->whereDate('created_at', '>=', $fromDate);
        })
        ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
            return $q2->whereBetween('created_at', [$fromDate, $toDate]);
        })
        ->where('status', '!=', 0)->where('plan_id', null)
        ->paginate(config('basic.paginate'));

        $funds->appends($search);
        $page_title = "Search Payment Logs";
        return view('admin.payment.logs', compact('funds', 'page_title'));
    }


    public function action(Request $request, $id)
    {

        $this->validate($request, [
            'id' => 'required',
            'status' => ['required', Rule::in(['1', '3'])],
        ]);

        $data = Fund::where('id', $request->id)->whereIn('status', [2])->with('user', 'gateway', 'property')->firstOrFail();

        $basic = (object)config('basic');

        $req = Purify::clean($request->all());
        $req = (object)$req;

        if ($request->status == '1') {
            $data->status = 1;
            $data->feedback = $request->feedback;
            $data->update();

            $user = $data->user;
            $amount = getAmount($data->amount);
            $trx = $data->transaction;

            $user->balance += $data->amount;
            $user->total_deposit += $data->amount;
            $user->save();

            $remarks = getAmount($data->amount) . ' ' . $basic->currency . ' payment amount has been approved';
            BasicService::makeTransaction($user, getAmount($data->amount), getAmount($data->charge), $trx_type = '+', $balance_type = 'deposit', $data->transaction, $remarks);


            if ($basic->deposit_commission == 1) {
                BasicService::setBonus($user, getAmount($data->amount), $type = 'deposit');
            }

            $this->sendMailSms($user, 'PAYMENT_APPROVED', [
                'amount' => getAmount($data->amount),
                'charge' => getAmount($data->charge),
                'gateway_name' => optional($data->gateway)->name,
                'currency' => $basic->currency,
                'transaction' => $data->transaction,
                'feedback' => $data->feedback,
            ]);


            $msg = [
                'amount' => getAmount($data->amount),
                'currency' => $basic->currency,
            ];
            $action = [
                "link" => '#',
                "icon" => "fas fa-money-bill-alt text-white"
            ];
            $this->userPushNotification($user, 'PAYMENT_APPROVED', $msg, $action);

            session()->flash('success', 'Approve Successfully');
            return back();

        } elseif ($request->status == '3') {
            $user = $data->user;
            $amount = getAmount($data->amount);
            $trx = $data->transaction;

            $data->status = 3;
            $data->feedback = $request->feedback;
            $data->update();

            $this->sendMailSms($user, $type = 'DEPOSIT_REJECTED', [
                'amount' => $amount,
                'currency' => $basic->currency,
                'method' => optional($data->gateway)->name,
                'transaction' => $trx,
                'feedback' => $data->feedback
            ]);

            $msg = [
                'amount' => getAmount($data->amount),
                'currency' => $basic->currency,
                'feedback' => $data->feedback,
            ];
            $action = [
                "link" => '#',
                "icon" => "fas fa-money-bill-alt text-white"
            ];
            $this->userPushNotification($user, 'PAYMENT_REJECTED', $msg, $action);

            session()->flash('success', 'Reject Successfully');
            return back();
        }
    }
}
