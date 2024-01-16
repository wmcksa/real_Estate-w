<?php

namespace App\Services;

use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Investment;
use App\Models\ManageProperty;
use App\Models\User;
use Carbon\Carbon;
use Facades\App\Services\NotifyMailService;
use Facades\App\Services\BasicService;
use Illuminate\Support\Facades\DB;

class InvestmentService
{
    use Upload, Notify;

    /**
     * @param $property
     * @param $request
     */

    public $net_profit, $net_loss;

    public function profitLossCalculate($request = null, $property = null)
    {
        if ($property->profit_type == 1 && $property->is_invest_type == 0 && $property->available_funding != $request->amount) {
            $this->net_profit = ($property->fixed_amount * $property->profit) / 100;
        } elseif ($property->profit_type == 1 && $property->is_invest_type == 0 && $property->available_funding == $request->amount) {
            $this->net_profit = ($request->amount * $property->profit) / 100;
        } elseif ($property->profit_type == 1 && $property->is_invest_type == 1) {
            $this->net_profit = ($request->amount * $property->profit) / 100;
        } else {
            $this->net_profit = $property->profit;
        }

        if ($property->loss_type == 1 && $property->is_invest_type == 0) {
            $this->net_loss = ($property->fixed_amount * $property->loss) / 100;
        } elseif ($property->loss_type == 1 && $property->is_invest_type == 0 && $property->available_funding == $request->amount) {
            $this->net_loss = ($request->amount * $property->loss) / 100;
        } elseif ($property->loss_type == 1 && $property->is_invest_type == 1) {
            $this->net_loss = ($request->amount * $property->loss) / 100;
        } else {
            $this->net_loss = $property->loss;
        }

    }

    public function forAllInvest($user, $property, $amount, $pay_installment, $trx)
    {
        $net_profit = $this->net_profit;
        $net_loss = $this->net_loss;

        // For Fixed invest
        if (($property->fixed_amount != null && ($property->fixed_amount == $amount || $property->available_funding == $amount)) && $pay_installment == null) {
            $investStatus = 1;
            $is_installment = 0;
            BasicService::makeInvest($user, $property, $amount, $net_profit, $net_loss, $investStatus, $is_installment, $trx);
        } // For installment invest
        elseif (($pay_installment != null && $amount == $property->installment_amount) && $property->fixed_amount != null) {
            $investStatus = 0;
            $is_installment = 1;
            BasicService::makeInvest($user, $property, $amount, $net_profit, $net_loss, $investStatus, $is_installment, $trx);
        } // For range invest
        elseif ($property->fixed_amount == null) {
            $investStatus = 1;
            $is_installment = 0;
            BasicService::makeInvest($user, $property, $amount, $net_profit, $net_loss, $investStatus, $is_installment, $trx);
        }
    }


    public function dueInstallmentPayment($user, $request, $pay_installment, $investment, $amount, $balance_type)
    {
        if ($pay_installment != null) {
            if ($investment->getInstallmentDate() == 0) {
                $installmentAmount = optional($investment->property)->installment_amount;
                $installmentLateFee = optional($investment->property)->installment_late_fee;
                $totalInstallmentAmount = $installmentAmount + $installmentLateFee;
                if ($amount > $totalInstallmentAmount || $amount < $totalInstallmentAmount) {
                    return back()->with('error', 'Invalid amount request. please pay ' . $totalInstallmentAmount);
                }
            } else {
                $installmentAmount = optional($investment->property)->installment_amount;
                if ($amount > $installmentAmount || $amount < $installmentAmount) {
                    return back()->with('error', 'Invalid amount request. please pay ' . $installmentAmount);
                }
            }
        } else {
            if ($investment->getInstallmentDate() == 0) {
                $dueAmount = (int)optional($investment->property)->fixed_amount - (int)$investment->amount;
                $installmentLateFee = optional($investment->property)->installment_late_fee;
                $totalDueAmountWithLateFee = $dueAmount + $installmentLateFee;
                if ($amount > $totalDueAmountWithLateFee || $amount < $totalDueAmountWithLateFee) {
                    return back()->with('error', 'Invalid amount request. please pay ' . $totalDueAmountWithLateFee);
                }
            } else {
                $dueAmount = (int)optional($investment->property)->fixed_amount - (int)$investment->amount;
                if ($amount > $dueAmount || $amount < $dueAmount) {
                    return back()->with('error', 'Invalid amount request. please pay ' . $dueAmount);
                }
            }
        }

        if ($pay_installment != null) {
            $subWithAvailableFund = optional($investment->property)->installment_amount;
            $investment->amount += optional($investment->property)->installment_amount;
            if ($investment->getInstallmentDate() == 0) {
                $investment->total_installment_late_fee += optional($investment->property)->installment_late_fee;
            }
        } else {
            $totalDueAmount = (int)optional($investment->property)->fixed_amount - (int)$investment->amount;
            $subWithAvailableFund = $totalDueAmount;
            $investment->amount += $totalDueAmount;

            if ($investment->getInstallmentDate() == 0) {
                $investment->total_installment_late_fee += optional($investment->property)->installment_late_fee;
            }
        }

        if ($pay_installment == null) {
            $dueInstallments = $investment->due_installments - $investment->due_installments;
        } else {
            $dueInstallments = $investment->due_installments - 1;
        }


        if ($dueInstallments == 0) {
            $investment->invest_status = 1;
            $investment->next_installment_date_start = null;
            $investment->next_installment_date_end = null;
            $returnTimeType = strtolower($investment->property->managetime->time_type);
            $returnTime = $investment->property->managetime->time;
            $func = $returnTimeType == 'days' ? 'addDays' : ($returnTimeType == 'months' ? 'addMonths' : 'addYears');
            $returnDate = Carbon::parse(optional($investment->property)->expire_date)->$func($returnTime);
            $investment->return_date = $returnDate;
        } else {
            $previousInstallmentLastDate = $investment->next_installment_date_end;
            $func2 = strtolower(optional($investment->property)->installment_duration_type) == 'days' ? 'addDays' : (strtolower(optional($investment->property)->installment_duration_type) == 'months' ? 'addMonths' : 'addYears');
            $nextInstallmentTimeStart = Carbon::parse($previousInstallmentLastDate)->$func2(optional($investment->property)->installment_duration);
            $nextInstallmentTimeEnd = $nextInstallmentTimeStart->copy()->$func2(optional($investment->property)->installment_duration);
            $investment->next_installment_date_start = $nextInstallmentTimeStart;
            $investment->next_installment_date_end = $nextInstallmentTimeEnd;
        }

        $investment->due_installments = $dueInstallments;
        $investment->save();

        $new_balance = getAmount($user->$balance_type - $amount); // new balance = user new balance
        $user->$balance_type = $new_balance;
        $user->total_invest += $request->amount;
        $user->save();

        $manageProperty = ManageProperty::findOrFail(optional($investment->property)->id);
        $new_available_fund = getAmount((int)$manageProperty->available_funding - $subWithAvailableFund);
        $manageProperty->available_funding = $new_available_fund;
        $manageProperty->save();
    }


    public function investmentProfitReturn($request, $investment, $nextReturnDate, $lastReturnDate, $now, $basic, $amount = null)
    {
        $user = User::where('id', $investment->user_id)->first();
        $investment = Investment::where('id', $investment->id)->first();

        $property = ManageProperty::with(['details'])->find($investment->property_id);

        if ($amount == null) {
            $amount = $request->amount;
        }

        if ($investment->how_many_times == null) {
            DB::table('investments')->where('id', $investment->id)->update([
                'return_date' => $nextReturnDate,
                'last_return_date' => $lastReturnDate
            ]);

            // Return Amount to user's Interest Balance

            $user->update([
                    'interest_balance' => DB::raw("interest_balance + $amount"),
                    'total_interest_balance' => DB::raw("total_interest_balance + $amount"),
                ]);


            $remarks = getAmount($amount) . ' ' . config('basic.currency') . ' Interest From ' . optional($property->details)->property_title;
            $transaction = BasicService::makeTransaction($user, $amount, 0, '+', 'interest_balance', strRandom(), $remarks, $property);

            NotifyMailService::investmentProfit($request, $user, $investment, $basic, $transaction, $amount);

        } elseif ($investment->how_many_times != null && $investment->how_many_times != 0) {

            $investment->update([
                'return_date' => $nextReturnDate,
                'last_return_date' => $lastReturnDate
            ]);

            // Return Amount to user's Interest Balance

            $new_balance = ($user->interest_balance + $amount);
            $user->interest_balance = $new_balance;
            $user->total_interest_balance += $amount;
            $user->save();
            $remarks = getAmount($amount) . ' ' . config('basic.currency') . ' Interest From ' . optional($property->details)->property_title;
            $transaction = BasicService::makeTransaction($user, $amount, 0, '+', 'interest_balance', strRandom(), $remarks, $property);


            $investment->how_many_times -= 1;
            $investment->save();

            NotifyMailService::investmentProfit($request, $user, $investment, $basic, $transaction, $amount);

            if ($investment->how_many_times == 0) {
                $new_invest = $investment;
                $new_invest->return_date = null;  // next Profit will get
                $new_invest->status = 1;
                $new_invest->save();
                $user = $new_invest->user;
                if ($investment->capital_back == 1) {
                    $capital = $investment->amount;
                    $new_balance = ($user->interest_balance + $capital);
                    $user->interest_balance = $new_balance;
                    $user->save();
                    $remarks = getAmount($capital) . ' ' . config('basic.currency') . ' Capital Back From ' . optional($property->details)->property_title;
                    $transaction = BasicService::makeTransaction($user, $capital, 0, '+', 'interest_balance', strRandom(), $remarks, $property);

                    NotifyMailService::investmentCapitalBack($user, $investment, $basic, $transaction, $capital);
                }
            }
        }
    }
}
