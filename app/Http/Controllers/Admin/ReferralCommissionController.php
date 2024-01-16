<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Configure;
use App\Models\Referral;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class ReferralCommissionController extends Controller
{
    use Upload, Notify;

    public function referralCommissionAction(Request $request)
    {
        $configure = Configure::firstOrNew();
        $reqData = Purify::clean($request->except('_token', '_method'));

        $configure->fill($reqData)->save();

        config(['basic.deposit_commission' => (int)$reqData['deposit_commission']]);
        config(['basic.investment_commission' => (int)$reqData['investment_commission']]);
        config(['basic.profit_commission' => (int)$reqData['profit_commission']]);
        $fp = fopen(base_path() . '/config/basic.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
        fclose($fp);

        return back()->with('success', 'Update Successfully.');
    }

    public function referralCommission()
    {
        $data['control'] = Configure::firstOrNew();
        $data['referrals'] = Referral::get();
        return view('admin.property.referral-commission', $data);
    }

    public function referralCommissionStore(Request $request)
    {
        $request->validate([
            'level*' => 'required|integer|min:1',
            'percent*' => 'required|numeric',
            'commission_type' => 'required',
        ]);

        Referral::where('commission_type', $request->commission_type)->delete();

        for ($i = 0; $i < count($request->level); $i++) {
            $referral = new Referral();
            $referral->commission_type = $request->commission_type;
            $referral->level = $request->level[$i];
            $referral->percent = $request->percent[$i];
            $referral->save();
        }

        return back()->with('success', 'Level Bonus Has been Updated.');
    }
}
