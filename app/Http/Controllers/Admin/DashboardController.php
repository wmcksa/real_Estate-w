<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use App\Models\Fund;
use App\Models\Investment;
use App\Models\ManageProperty;
use App\Models\PayoutLog;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Purify\Facades\Purify;
use \Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    use Upload;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    public function forbidden()
    {
        return view('admin.errors.403');
    }


    public function dashboard()
    {
        $data['userRecord'] = collect(User::selectRaw('COUNT(id) AS totalUser')
            ->selectRaw('count(CASE WHEN status = 1  THEN id END) AS activeUser')
            ->selectRaw('count(CASE WHEN status = 0  THEN id END) AS bannedUser')
            ->selectRaw('count(CASE WHEN identity_verify = 2 AND address_verify =2 THEN id END) AS totalVerifiedUser')
            ->selectRaw('SUM(balance) AS totalUserBalance')
            ->selectRaw('SUM(interest_balance) AS totalInterestBalance')
            ->selectRaw('SUM(joining_bonus) AS totalJoiningBonus')
            ->selectRaw('SUM(total_referral_bonous) AS totalReferralBonus')
            ->selectRaw('COUNT((CASE WHEN created_at = CURDATE()  THEN id END)) AS todayJoin')
            ->get()->makeHidden(['fullname', 'mobile'])->toArray())->collapse();

        $data['funding'] = collect(Fund::selectRaw('SUM(CASE WHEN status = 1 AND property_id IS NULL THEN amount END) AS totalAmountReceived')
            ->selectRaw('SUM((CASE WHEN status = 1 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN amount END)) AS monthlyDepositAmount')
            ->selectRaw('SUM((CASE WHEN status = 1 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN charge END)) AS monthlyDepositCharge')
            ->selectRaw('COUNT(CASE WHEN status = 2  THEN id END) AS pendingDeposit')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN charge END) AS totalChargeReceived')
            ->selectRaw('SUM((CASE WHEN created_at = CURDATE() AND status = 1 AND property_id IS NULL THEN amount END)) AS todayDeposit')
            ->get()->toArray())->collapse();

        $data['payout'] = collect(PayoutLog::selectRaw('COUNT(CASE WHEN status = 1  THEN id END) AS pendingPayout')
            ->selectRaw('SUM(CASE WHEN status = 2 THEN amount END) AS totalPayoutAmount')
            ->selectRaw('SUM((CASE WHEN status = 2 AND created_at >= CURDATE()  THEN amount END)) AS todayPayoutAmount')
            ->selectRaw('SUM((CASE WHEN status = 2 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN amount END)) AS monthlyPayoutAmount')
            ->selectRaw('SUM(CASE WHEN status = 2 THEN charge END) AS totalPayoutCharge')
            ->selectRaw('SUM((CASE WHEN status = 2 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN charge END)) AS monthlyPayoutCharge')
            ->get()->toArray())->collapse();


        $data['property'] = collect(ManageProperty::selectRaw('COUNT(id) AS totalProperty')
            ->selectRaw('count(CASE WHEN status = 1 THEN id END) AS activeProperty')
            ->selectRaw('count(CASE WHEN expire_date > now() AND start_date < now() AND status = 1 THEN id END) AS runningProperty')
            ->selectRaw('count(CASE WHEN start_date > now() AND status = 1 THEN id END) AS upcomingProperty')
            ->selectRaw('count(CASE WHEN expire_date < now() AND status = 1 THEN id END) AS expiredProperty')
            ->get()->makeHidden(['investmentAmount', 'limitamenity', 'allamenity', 'managetime'])->toArray())->collapse();


        $data['investment'] = collect(Investment::with('property')->selectRaw('COUNT(id) AS totalInvestment')
            ->selectRaw('COUNT(CASE WHEN is_active = 1 THEN id END) AS activeInvestment')
            ->selectRaw('COUNT(CASE WHEN is_active = 0 THEN id END) AS deactiveInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 1 AND status = 0 AND is_active = 1 THEN id END) AS runningInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 0 AND status = 0 AND is_active = 1 THEN id END) AS dueInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 1 AND status = 0 AND is_active = 1 THEN id END) AS expiredInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 1 AND status = 1 AND is_active = 1  THEN status END) AS completeInvestment')
            ->selectRaw('COUNT((CASE WHEN is_active = 1 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN id END)) AS monthlyTotalInvestment')
            ->selectRaw('COUNT((CASE WHEN is_active = 1 AND created_at = CURDATE() THEN id END)) AS todayInvestment')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN amount END) AS totalInvestAmount')
            ->selectRaw('SUM((CASE WHEN is_active = 1 AND created_at >=  DATE_SUB(CURRENT_DATE() , INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) THEN amount END)) AS monthlyInvestmentAmount')
            ->selectRaw('SUM((CASE WHEN is_active = 1 AND created_at = CURDATE() THEN amount END)) AS todayInvestmentAmount')
            ->get()->makeHidden('nextPayment')->toArray())->collapse();


        $data['runningInvestment'] = Investment::with('property')
                        ->whereHas('property', function ($query){
                            $query->where('expire_date', '>', now());
                        })
            ->where('invest_status', 1)
            ->where('status', 0)
            ->where('is_active', 1)
            ->count();


        $data['expiredInvestment'] = Investment::with('property')
            ->whereHas('property', function ($query){
                $query->where('expire_date', '<', now());
            })
            ->where('invest_status', 1)
            ->where('status', 0)
            ->where('is_active', 1)
            ->count();


        $data['tickets'] = collect(Ticket::where('created_at', '>', Carbon::now()->subDays(30))
            ->selectRaw('count(CASE WHEN status = 3  THEN status END) AS closed')
            ->selectRaw('count(CASE WHEN status = 2  THEN status END) AS replied')
            ->selectRaw('count(CASE WHEN status = 1  THEN status END) AS answered')
            ->selectRaw('count(CASE WHEN status = 0  THEN status END) AS pending')
            ->get()->toArray())->collapse();


        $dailyInvest = $this->dayList();
        Investment::whereMonth('created_at', Carbon::now()->month)
            ->select(
                DB::raw('SUM(CASE WHEN is_active = 1 THEN amount END) AS totalAmount'),
                DB::raw('DATE_FORMAT(created_at,"Day %d") as date')
            )
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get()->makeHidden('nextPayment')->map(function ($item) use ($dailyInvest) {
                $dailyInvest->put($item['date'], round($item['totalAmount'], 2));
            });
        $statistics['investment'] = $dailyInvest;

        $dailyDeposit = $this->dayList();
        Fund::whereMonth('created_at', Carbon::now()->month)
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw('DATE_FORMAT(created_at,"Day %d") as date')
            )
            ->where('status',1)
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get()->map(function ($item) use ($dailyDeposit) {
                $dailyDeposit->put($item['date'], round($item['totalAmount'], 2));
            });
        $statistics['deposit'] = $dailyDeposit;

        $dailyGaveProfit = $this->dayList();
         Transaction::whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('SUM((CASE WHEN remarks LIKE "%Interest From%" THEN amount END)) AS totalAmount')
            ->selectRaw('DATE_FORMAT(created_at,"Day %d") as date')
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get()->map(function ($item) use ($dailyGaveProfit) {
                $dailyGaveProfit->put($item['date'], round($item['totalAmount'], 2));
            });
        $statistics['gaveProfit'] = $dailyGaveProfit;


        $dailyPayout = $this->dayList();
        PayoutLog::whereMonth('created_at', Carbon::now()->month)
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw('DATE_FORMAT(created_at,"Day %d") as date')
            )
            ->where('status',2)
            ->groupBy(DB::raw("DATE(created_at)"))
            ->get()->map(function ($item) use ($dailyPayout) {
                $dailyPayout->put($item['date'], round($item['totalAmount'], 2));
            });
        $statistics['payout'] = $dailyPayout;
        $statistics['schedule'] = $this->dayList();

        $data['latestUser'] = User::with('userBadge.details')->latest()->limit(5)->get();

        return view('admin.dashboard', $data, compact('statistics', 'statistics'));
    }

    public function dayList()
    {
        $totalDays = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $daysByMonth = [];
        for ($i = 1; $i <= $totalDays; $i++) {
            array_push($daysByMonth, ['Day ' . sprintf("%02d", $i) => 0]);
        }

        return collect($daysByMonth)->collapse();
    }

    public function profile()
    {
        $admin = $this->user;
        return view('admin.profile', compact('admin'));
    }


    public function profileUpdate(Request $request)
    {
        $req = Purify::clean($request->except('_token', '_method'));
        $rules = [
            'name' => 'sometimes|required',
            'username' => 'sometimes|required|unique:admins,username,' . $this->user->id,
            'email' => 'sometimes|required|email|unique:admins,email,' . $this->user->id,
            'phone' => 'sometimes|required',
            'address' => 'sometimes|required',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = $this->user;
        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = $this->uploadImage($request->image, config('location.admin.path'), config('location.admin.size'), $old);
            } catch (\Exception $exp) {
                return back()->with('error', 'Image could not be uploaded.');
            }
        }
        $user->name = $req['name'];
        $user->username = $req['username'];
        $user->email = $req['email'];
        $user->phone = $req['phone'];
        $user->address = $req['address'];
        $user->save();

        return back()->with('success', 'Updated Successfully.');
    }


    public function password()
    {
        return view('admin.password');
    }

    public function passwordUpdate(Request $request)
    {
        $req = Purify::clean($request->all());

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $request = (object)$req;
        $user = $this->user;
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', "Password didn't match");
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        return back()->with('success', 'Password has been Changed');
    }
}
