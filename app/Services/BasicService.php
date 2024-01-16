<?php

namespace App\Services;

use App\Http\Traits\Notify;
use App\Models\Badge;
use App\Models\Configure;
use App\Models\Investment;
use App\Models\Ranking;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Image;

class BasicService
{
    use Notify;

    public function validateImage(object $getImage, string $path)
    {
        if ($getImage->getClientOriginalExtension() == 'jpg' or $getImage->getClientOriginalName() == 'jpeg' or $getImage->getClientOriginalName() == 'png') {
            $image = uniqid() . '.' . $getImage->getClientOriginalExtension();
        } else {
            $image = uniqid() . '.jpg';
        }
        Image::make($getImage->getRealPath())->resize(300, 250)->save($path . $image);
        return $image;
    }

    public function validateDate(string $date)
    {
        if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/", $date)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateKeyword(string $search, string $keyword)
    {
        return preg_match('~' . preg_quote($search, '~') . '~i', $keyword);
    }

    public function cryptoQR($wallet, $amount, $crypto = null)
    {

        $varb = $wallet . "?amount=" . $amount;
        return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
    }

    public function preparePaymentUpgradation($order)
    {
        $basic = (object) config('basic');
        $gateway = $order->gateway;

        if ($order->status == 0) {
            $order['status'] = 1;
            $order->update();

            $user = $order->user;

            $amount = getAmount($order->amount);
            $trx = $order->transaction;

            $user->balance += $order->amount;
            $user->save();

            $this->makeTransaction($user, getAmount($order->amount), getAmount($order->charge), $trx_type = '+', $balance_type = 'deposit', $order->transaction, $remarks = 'Deposit Via ' . $gateway->name);

            if ($basic->deposit_commission == 1) {
                $this->setBonus($user, getAmount($order->amount), $type = 'deposit');
            }

            $currentDate = dateTime(Carbon::now());
            $msg = [
                'username' => $user->username,
                'amount' => getAmount($order->amount),
                'currency' => $basic->currency,
                'gateway' => $gateway->name
            ];
            $action = [
                "link" => route('admin.user.fundLog', $user->id),
                "icon" => "fa fa-money-bill-alt text-white"
            ];
            $this->adminPushNotification('ADMIN_NOTIFY_FUND_DEPOSIT_PAYMENT_COMPLETE', $msg, $action);
            $this->mailToAdmin($type = 'ADMIN_MAIL_FUND_DEPOSIT_PAYMENT_COMPLETE', [
                'username' => $user->username,
                'amount' => getAmount($order->amount),
                'currency' => $basic->currency,
                'gateway' => $gateway->name,
                'date'  => $currentDate,
            ]);

            $userAction = [
                "link" => route('user.fund-history'),
                "icon" => "fa fa-money-bill-alt text-white"
            ];

            $this->userPushNotification($user, 'USER_NOTIFY_FUND_DEPOSIT_PAYMENT_COMPLETE', $msg, $userAction);
            $this->sendMailSms($user, 'USER_MAIL_FUND_DEPOSIT_PAYMENT_COMPLETE', [
                'gateway_name' => $gateway->name,
                'amount' => getAmount($order->amount),
                'charge' => getAmount($order->charge),
                'currency' => $basic->currency,
                'transaction' => $order->transaction,
                'remaining_balance' => getAmount($user->balance)
            ]);
            session()->forget('amount');
            session()->forget('plan_id');
        }
    }


    public function setBonus($user, $amount, $commissionType = ''){

        $basic = (object) config('basic');
        $userId = $user->id;
        $i = 1;
        $level = \App\Models\Referral::where('commission_type', $commissionType)->count();

        while ($userId != "" || $userId != "0" || $i < $level) {
            $me = \App\Models\User::with('referral')->find($userId);
            $refer = $me->referral;

            if (!$refer) {
                break;
            }

            $commission = \App\Models\Referral::where('commission_type', $commissionType)->where('level', $i)->first();
            if (!$commission) {
                break;
            }

            $extra_com = 0;
            if ($refer->premium_user == 1){
                $extra_com = ($amount * (int)$commission->extra_bonus) / 100;
            }

            $com = (($amount * $commission->percent) / 100) + $extra_com;
            $new_bal = getAmount($refer->interest_balance + $com);
            $refer->interest_balance = $new_bal;
            $refer->total_interest_balance += $com;
            if ($commissionType == 'deposit'){
                $refer->deposit_referral_bonous += $com;
            }elseif($commissionType == 'invest'){
                $refer->invest_referral_bonous += $com;
            }elseif($commissionType == 'profit_commission'){
                $refer->profit_referral_bonous += $com;
            }elseif($commissionType == 'badge_commission'){
                $refer->badge_referral_bonous += $com;
            }elseif($commissionType == 'joining_bonus'){
                $refer->total_referral_joining_bonus += $com;
            }

            $refer->total_referral_bonous += $com;
            $refer->save();

            $trx = strRandom();
            $balance_type = 'interest_balance';

            $remarks = ' level ' . $i . ' Referral bonus From ' . $user->username;

            $this->makeTransaction($refer, $com, 0, '+', $balance_type, $trx, $remarks);


            $bonus = new \App\Models\ReferralBonus();
            $bonus->from_user_id = $refer->id;
            $bonus->to_user_id = $user->id;
            $bonus->level = $i;
            $bonus->amount = getAmount($com);
            $bonus->main_balance = $new_bal;
            $bonus->transaction = $trx;
            $bonus->type = $commissionType;
            $bonus->remarks = $remarks;
            $bonus->save();


            $this->sendMailSms($refer, $type = 'REFERRAL_BONUS', [
                'transaction_id' => $trx,
                'amount' => getAmount($com),
                'currency' => $basic->currency_symbol,
                'bonus_from' => $user->username,
                'final_balance' => $refer->interest_balance,
                'level' => $i
            ]);


            $msg = [
                'bonus_from' => $user->username,
                'amount' => getAmount($com),
                'currency' => $basic->currency_symbol,
                'level' => $i
            ];
            $action = [
                "link" => route('user.referral.bonus'),
                "icon" => "fa fa-money-bill-alt"
            ];
            $this->userPushNotification($refer,'REFERRAL_BONUS', $msg, $action);

            $userId = $refer->id;
            $i++;
        }
        return 0;
    }


    /**
     * @param $user
     * @param $amount
     * @param $charge
     * @param $trx_type
     * @param $balance_type
     * @param $trx_id
     * @param $remarks
     */

    public function makeTransaction($user, $amount, $charge, $trx_type = null, $balance_type, $trx_id, $remarks = null, $property = null, $investment=null)
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->property_id = ($property != null ? $property->id : null);
        $transaction->investment_id = ($investment != null ? $investment->id : null);
        $transaction->amount = getAmount($amount);
        $transaction->charge = $charge;
        $transaction->trx_type = $trx_type;
        $transaction->balance_type = $balance_type;
        $transaction->final_balance = $user[$balance_type];
        $transaction->trx_id = $trx_id;
        $transaction->remarks = $remarks;
        $transaction->save();
        return $transaction;
    }


    /**
     * @param $user
     * @param $plan
     * @param $amount
     * @param $profit
     * @param $maturity
     * @param $timeManage
     * @param $trx
     */
    public function makeInvest($user, $property, $amount, $profit, $loss, $investStatus, $is_installment, $trx, $payment_status=1)
    {
        $returnTimeType = strtolower($property->managetime->time_type);
        $func = $returnTimeType == 'days' ? 'addDays' : ($returnTimeType == 'months' ? 'addMonths' : 'addYears');
        $returnTime     = $property->managetime->time;
        $returnDate = Carbon::parse($property->expire_date)->$func($returnTime);

        $func2 = strtolower($property->installment_duration_type) == 'days' ? 'addDays' : (strtolower($property->installment_duration_type) == 'months' ? 'addMonths' : 'addYears');
        $nextInstallmentTimeStart = now()->$func2($property->installment_duration);
        $nextInstallmentTimeEnd = $nextInstallmentTimeStart->copy()->$func2($property->installment_duration);


        if ($property->total_installments && $investStatus == 0){
            $dueInstallments = $property->total_installments - 1;
            if ($dueInstallments == 0){
                $investStatus = 1;
            }
        }else{
            $dueInstallments = null;
        }

        $invest = new Investment();
        $invest->user_id = $user->id;
        $invest->property_id = $property->id;
        $invest->amount = $amount;
        $invest->profit = $property->profit;
        $invest->profit_type = $property->profit_type;
        $invest->net_profit = $profit;
        $invest->loss = $property->loss;
        $invest->loss_type = $property->loss_type;
        $invest->net_loss = $loss;
        $invest->is_return_type = $property->is_return_type;
        $invest->return_time = $returnTime;
        $invest->return_time_type = $returnTimeType;
        $invest->return_date = ($dueInstallments != 0 && $dueInstallments != null ? null : $returnDate);
        $invest->how_many_times = ($property->how_many_times == null ? null : $property->how_many_times);
        $invest->last_return_date = null;
        $invest->is_installment = ($is_installment == 1 ? $property->is_installment : 0);
        $invest->total_installments = ($is_installment == 1 ? $property->total_installments : null);
        $invest->due_installments = ($is_installment == 1 ? $property->total_installments - 1 : null);
        $invest->next_installment_date_start = ($is_installment == 1 ? $nextInstallmentTimeStart : null);
        $invest->next_installment_date_end = ($is_installment == 1 ? $nextInstallmentTimeEnd : null);
        $invest->invest_status = $investStatus;
        $invest->payment_status = $payment_status;
        $invest->status = 0;
        $invest->capital_back = $property->is_capital_back;
        $invest->trx = $trx;
        $invest->save();

        if ($amount > $property->available_funding){
            $property->available_funding = $property->available_funding - $property->available_funding;
            $property->save();
        }else{
            $property->available_funding = $property->available_funding - $amount;
            $property->save();
        }
    }


    public function getInvestorCurrentBadge($user){

        $interestBalance = $user->total_interest_balance;
        $investBalance = $user->total_invest;
        $depositBalance = $user->total_deposit;

        $badges = Badge::where([
            ['min_invest', '<=', $investBalance],
            ['min_deposit', '<=', $depositBalance],
            ['min_earning', '<=', $interestBalance]])->where('status', 1)->get();

        $userPreviousRank = (json_decode($user->all_badges) == null ? []: json_decode($user->all_badges));

        if (count($badges) > 0) {
            $userBadge = [
                'min_invest' => '0',
                'min_deposit' => '0',
                'min_earning' => '0'
            ];
            $allBadges = [];

            foreach ($badges as $badge) {
                $userBadge = (object)$userBadge;
                if (($userBadge->min_invest <= $badge->min_invest) && ($userBadge->min_deposit <= $badge->min_deposit) && ($userBadge->min_earning <= $badge->min_earning)) {
                    $userBadge = $badge;
                    $user->last_level = $userBadge->id;
                    $allBadges [] = $userBadge->id;
                    $user->save();
                }
            }

            $recentUpdateBadges = array_diff($allBadges, $userPreviousRank);

            $badgeSettings = Configure::select('bonus')->first();
            $basic = (object)config('basic');

            if (count($recentUpdateBadges) > 0 && $badgeSettings->bonus){
                foreach ($recentUpdateBadges as $recentBadge){
                    $singleRank = Badge::with('details')->find($recentBadge);
                    $user->total_badge_bonous =(int)$user->total_badge_bonous + (int)$singleRank->bonus;
                    $user->interest_balance   = (int)$user->interest_balance + (int)$singleRank->bonus;
                    $user->total_interest_balance   = (int)$user->total_interest_balance + (int)$singleRank->bonus;
                    $user->save();

                    $amount = $singleRank->bonus;
                    $trx = strRandom();
                    $remarks = 'You got ' . $basic->currency_symbol.$singleRank->bonus. ' '.optional($singleRank->details)->rank_level. ' ranking bonus';
                    BasicService::makeTransaction($user, $amount, 0, $trx_type = '+', 'interest_balance', $trx, $remarks);

                    if ($basic->badge_commission == 1) {
                        $this->setBonus($user, getAmount($singleRank->bonus), $type = 'badge_commission');
                    }
                }
            }

            $user->all_badges = $allBadges;
            $user->save();

            return $userBadge;
        }else{
            $userBadge = null;
            return $userBadge;
        }
    }
}
