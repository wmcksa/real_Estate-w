<?php

namespace App\Console\Commands;
use App\Models\Configure;
use App\Models\Ranking;
use Facades\App\Services\BasicService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateBadgeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badge:cron';

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
        if (Auth::check() == true){
            $user = Auth::user();

            $interestBalance = $user->total_interest_balance;
            $investBalance = $user->total_invest;
            $depositBalance = $user->total_deposit;

            $userPreviousRank = json_decode($user->all_badges);

            $badges = Ranking::where([
                ['min_invest', '<=', $investBalance],
                ['min_deposit', '<=', $depositBalance],
                ['min_earning', '<=', $interestBalance]])->where('status', 1)->get();

            if (count($badges) > 0){
                $allBadges = [];
                foreach ($badges as $badge){
                    $user->last_lavel = $badge->id;
                    $allBadges [] = $badge->id;
                    $user->save();
                }

                $recentUpdateBadges = array_diff($allBadges, $userPreviousRank);
                $badgeSettings = Configure::select('bonus')->first();
                $basic = (object)config('basic');
                if (count($recentUpdateBadges) > 0 && $badgeSettings->bonus){
                   foreach ($recentUpdateBadges as $recentBadge){
                       $singleRank = DB::table('rankings')->select('bonus')->find($recentBadge);
                       $user->total_badge_bonous += (int)$singleRank->bonus;
                       $user->save();

                       $amount = $singleRank->bonus;
                       $trx = strRandom();
                       $remarks = 'You got ' . $basic->currency_symbol.$singleRank->bonus. ' '.$singleRank->rank_lavel. 'ranking bonus';
                       BasicService::makeTransaction($user, $amount, 0, $trx_type = '+', 'interest_balance', $trx, $remarks);

                       if ($basic->badge_commission == 1) {
                           BasicService::setBonus($user, getAmount($singleRank->bonus), $type = 'badge_commission');
                       }
                   }
                }

                $user->all_badges = $allBadges;
                $user->save();
            }

        }
    }
}
