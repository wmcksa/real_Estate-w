<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\ManageProperty;
use App\Models\User;
use Facades\App\Services\BasicService;
use Facades\App\Services\InvestmentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MainCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    static public function handle()
    {

        $now = Carbon::parse(Carbon::now());
        $basic = (object)config('basic');
        $request = null;

        $manageProperties = ManageProperty::with(['getInvestment', 'getInvestment.user', 'details'])->where('is_payment', 1)->where('status', 1)->get();
        
foreach ($manageProperties as $property) {

            $returnTimeType = strtolower(optional($property->managetime)->time_type);
            $func = $returnTimeType == 'days' ? 'addDays' : ($returnTimeType == 'months' ? 'addMonths' : 'addYears');

            DB::table('investments')->where('property_id', $property->id)
                ->where('status', 0)->where('is_active', 1)->where('invest_status', 1)
                ->whereDate('return_date', '<=', now())
                ->orderBy('id')->chunk(50, function ($allInvest) use ($returnTimeType, $func, $now, $basic, $property, $request) {
                    
                    
                    foreach ($allInvest as $investment) {
                        $returnTime = (int)optional($property->managetime)->time;
                        $nextReturnDate = now()->$func($returnTime);
                        $lastReturnDate = now();

                        $amount = $investment->profit_type == 0 ? (int)$investment->profit : (int)$investment->net_profit;

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

        }
    }
}
