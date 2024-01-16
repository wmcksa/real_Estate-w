<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\ManageProperty;
use App\Models\User;
use Carbon\Carbon;
use Facades\App\Services\BasicService;
use Facades\App\Services\InvestmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function investments(Request $request, $type = 'all')
    {
        $handleInvestment = config('handleInvestment');

        $types = array_keys($handleInvestment);

        abort_if(!in_array($type, $types), 404);

        $title = $handleInvestment[$type]['title'];

        $investments = Investment::with('user', 'property.details')
            ->when($type == "expired", function ($query) {
                $query->whereHas('property', function ($qry) {
                    $qry->where('expire_date', '<', now());
                });
            })
            ->when($type == "running", function ($query) {
                $query->whereHas('property', function ($qry) {
                    $qry->where('expire_date', '>', now());
                });
            })
            ->orderBy('id', 'DESC')
            ->when($type != "all", function ($query) use ($type) {
                $query->where('invest_status', $type == 'due' ? 0 : 1) // investment payment status
                ->where('status', $type == 'completed' ? 1 : 0) // profit return status
                ->where('is_active', 1) // investment status
                ->groupBy('property_id');
            })
            ->paginate(config('basic.paginate'));

        return view($handleInvestment[$type]['investment_view'], compact('investments', 'title'));
    }


    public function investPaymentAllUser(Request $request, $id)
    {

        $property = ManageProperty::with(['getInvestment', 'getInvestment.user', 'details'])->find($id);

        $returnTimeType = strtolower(optional($property->managetime)->time_type);

        $func = $returnTimeType == 'days' ? 'addDays' : ($returnTimeType == 'months' ? 'addMonths' : 'addYears');

        $now = now();

        $basic = (object)config('basic');


        DB::table('investments')->where('property_id', $property->id)
            ->where('status', 0)->where('is_active', 1)->where('invest_status', 1)
            ->where('return_date', '<', now())
            ->orderBy('id')->chunk(50, function ($allInvest) use ($returnTimeType, $func, $now, $basic, $property, $request) {
                foreach ($allInvest as $investment) {
                    $returnTime = (int)optional($property->managetime)->time;
                    $nextReturnDate = now()->$func($returnTime);
                    $lastReturnDate = now();

                    $amount = ($investment->profit_type == 0) ? $request->amount : ($investment->amount * $request->get_profit) / 100;

                    if ($investment->profit_type == 0) {
                        $records['profit'] = $amount;
                    } else {
                        $records['net_profit'] = $amount;
                    }

                    $records['return_date'] = $nextReturnDate;
                    $records['last_return_date'] = $lastReturnDate;
                    DB::table('investments')
                        ->where('id', $investment->id)
                        ->update($records);

                    InvestmentService::investmentProfitReturn($request, $investment, $nextReturnDate, $lastReturnDate, $now, $basic, $amount);

                    if ($basic->profit_commission == 1) {
                        $user = User::find($investment->user_id);
                        BasicService::setBonus($user, $amount, 'profit_commission');
                    }
                }
            });


        return back()->with('success', 'Payment successfully completed');
    }


    public function investPaymentSingleUser(Request $request, $id)
    {

        $investment = Investment::with('user', 'property.details')->where('status', 0)->findOrFail($id);

        $returnTimeType = strtolower(optional($investment->property->managetime)->time_type);
        $func = $returnTimeType == 'days' ? 'addDays' : ($returnTimeType == 'months' ? 'addMonths' : 'addYears');
        $now = Carbon::parse(Carbon::now());
        $basic = (object)config('basic');

        $returnTime = (int)optional($investment->property->managetime)->time;
        $nextReturnDate = now()->$func($returnTime);
        $lastReturnDate = now();


        $amount = $investment->amount * $request->get_profit / 100;
        $investment->net_profit = $amount;
        $investment->save();
        InvestmentService::investmentProfitReturn($request, $investment, $nextReturnDate, $lastReturnDate, $now, $basic, $amount);

        $user = $investment->user;
        if ($basic->profit_commission == 1) {
            BasicService::setBonus($user, $amount, $type = 'profit_commission');
        }

        return back()->with('success', 'Payment Successfully Completed');
    }


    public function seeInvestedUser($id, $type = null)
    {

        $handleInvestedUser = config('handleInvestedUser');

        $types = array_keys($handleInvestedUser);

        abort_if(!in_array($type, $types), 404);

        $title = $handleInvestedUser[$type]['title'];

        $investedUser = Investment::with('user', 'property.details')
            ->where('invest_status', $type == 'due' ? 0 : 1)
            ->where('property_id', $id)
            ->where('status', $type == 'completed' ? 1 : 0)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();

        return view($handleInvestedUser[$type]['invested_user_view'], compact('investedUser', 'title'));
    }

    public function investmentsSearch(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $investments = Investment::with('user', 'property.details')
            ->when(isset($search['user']), function ($query) use ($search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('firstname', 'LIKE', "%{$search['user']}%")
                        ->orWhere('lastname', 'LIKE', "%{$search['user']}%")
                        ->orWhere('username', 'LIKE', "%{$search['user']}%");
                });
            })
            ->when(isset($search['property']), function ($query) use ($search) {
                return $query->whereHas('property.details', function ($q) use ($search) {
                    $q->where('property_title', 'LIKE', '%' . $search['property'] . '%');
                });
            })
            ->when(isset($search['invest_status']), function ($query) use ($search) {
                return $query->where('invest_status', $search['invest_status']);
            })
            ->when(isset($search['return_status']), function ($query) use ($search) {
                return $query->where('status', $search['return_status']);
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('is_active', $search['status']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'DESC')->paginate(config('basic.paginate'));

        $investments = $investments->appends($search);
        $title = 'All Investments';
        return view('admin.transaction.investLog', compact('investments', 'title'));
    }

    public function investmentDetails($id)
    {
        $singleInvestDetails = Investment::with('property.details')->findOrFail($id);
        return view('admin.transaction.investDetails', compact('singleInvestDetails'));
    }

    public function investActive(Request $request)
    {
        $invest = Investment::with('user', 'property.details:id,property_title')->findOrFail($request->invest_id);
        $invest->is_active = 1;
        $invest->save();

        return back()->with('success', ' investment active.');
    }

    public function activeMultiple(Request $request)
    {

        $investmentIds = explode(',', $request->investment_id);

        Investment::whereIn('id', $investmentIds)->update([
            'is_active' => 1,
            'deactive_reason' => null,
        ]);
        session()->flash('success', __('Investment Has Been Active Successfully!'));
        return back();
    }

    public function investDeactive(Request $request)
    {

        $purifiedData = Purify::clean($request->except('_token', '_method'));

        $rules = [
            'deactive_reason' => 'required',
        ];

        $message = [
            'deactive_reason.required' => __('Please write your investment deactive reason?'),
        ];

        $validate = Validator::make($purifiedData, $rules, $message);
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $invest = Investment::with('user', 'property.details')->findOrFail($request->invest_id);

        $invest->is_active = 0;
        $invest->deactive_reason = $request->deactive_reason;
        $invest->save();

        return back()->with('success', 'deactive successfully.');
    }

    public function deactiveMultiple(Request $request)
    {

        $investmentIds = explode(',', $request->investment_id);

        if ($request->deactive_reason == '') {
            session()->flash('error', __('Deactive reason required.'));
            return back();
        } else {
            Investment::whereIn('id', $investmentIds)->update([
                'is_active' => 0,
                'deactive_reason' => $request->deactive_reason,
            ]);
            session()->flash('success', __('Investment Has Been Deactive Successfully!'));
            return back();
        }
    }

    public function deleteMultiple(Request $request)
    {
        $selectedInvestId = $request->invest_id;
        $investIdArray = explode(",", $selectedInvestId);
        if ($investIdArray != '') {
            foreach ($investIdArray as $id) {
                Investment::findOrFail($id)->delete();
            }
        }
        return back()->with('success', 'Investment deleted successfully!');
    }

    public function disbursementType(Request $request)
    {
        if ($request->isval == 'on') {
            $manageProperty = ManageProperty::findOrFail($request->dataid);
            $manageProperty->is_payment = 0;
            $manageProperty->save();
        } else {
            $manageProperty = ManageProperty::findOrFail($request->dataid);
            $manageProperty->is_payment = 1;
            $manageProperty->save();
        }
    }
}
