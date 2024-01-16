<?php

namespace App\Services;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\ManageProperty;
use Carbon\Carbon;


class NotifyMailService
{
    use Upload, Notify;

    public function investmentProfit($request, $user, $investment, $basic, $transaction, $amount = null){

        $property = ManageProperty::with(['details'])->find($investment->property_id);
        $currentDate = Carbon::now();
        $msg = [
            'username'  => $user->username,
            'amount'    => $amount == null ? getAmount($request->amount) : getAmount($amount),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title
        ];
        $action = [
            "link" => route('admin.transaction'),
            "icon" => "fa fa-money-bill-alt"
        ];

        $userAction = [
            "link" => route('user.transaction'),
            "icon" => "fa fa-money-bill-alt"
        ];

        $this->adminPushNotification('INVEST_PROFIT_NOTIFY_TO_ADMIN', $msg, $action);
        $this->userPushNotification($user, 'INVEST_PROFIT_NOTIFY_TO_USER', $msg, $userAction);

        $this->sendMailSms($user, 'INVEST_PROFIT_MAIL_TO_USER', [
            'username'       => $user->username,
            'amount'    => $amount == null ? getAmount($request->amount) : getAmount($amount),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $transaction->trx_id,
            'date'           => $currentDate,
        ]);

        $this->mailToAdmin('INVEST_PROFIT_MAIL_TO_ADMIN', [
            'username'       => $user->username,
            'amount'    => $amount == null ? getAmount($request->amount) : getAmount($amount),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $transaction->trx_id,
            'date'           => $currentDate,
        ]);
    }

    public function investmentCapitalBack($user, $investment, $basic, $transaction, $capital){
        $currentDate = Carbon::now();
        $property = ManageProperty::with(['details'])->find($investment->property_id);
        $msg = [
            'username'  => $user->username,
            'amount'    => getAmount($capital),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title
        ];
        $action = [
            "link" => route('admin.transaction'),
            "icon" => "fa fa-money-bill-alt"
        ];

        $userAction = [
            "link" => route('user.transaction'),
            "icon" => "fa fa-money-bill-alt"
        ];

        $this->adminPushNotification('INVESTMENT_CAPITAL_BACK_NOTIFY_TO_ADMIN', $msg, $action);
        $this->userPushNotification($user, 'INVESTMENT_CAPITAL_BACK_NOTIFY_TO_USER', $msg, $userAction);

        $this->sendMailSms($user, $type = 'INVESTMENT_CAPITAL_BACK_MAIL_TO_USER', [
            'username'       => $user->username,
            'amount'    => getAmount($investment->net_profit),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $transaction->trx_id,
            'date'           => $currentDate,
        ]);

        $this->mailToAdmin('INVESTMENT_CAPITAL_BACK_MAIL_TO_ADMIN', [
            'username'       => $user->username,
            'amount'    => getAmount($investment->net_profit),
            'currency'  => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $transaction->trx_id,
            'date'           => $currentDate,
        ]);
    }
}
