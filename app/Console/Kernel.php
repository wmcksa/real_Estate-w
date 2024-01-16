<?php

namespace App\Console;

use App\Console\Commands\BlockIoIPN;
use App\Console\Commands\MainCron;
use App\Console\Commands\UpdateBadgeCron;
use App\Console\Commands\PayoutCryptoCurrencyUpdateCron;
use App\Console\Commands\PayoutCurrencyUpdateCron;
use App\Console\Commands\BinanceCron;
use App\Models\Configure;
use App\Models\Gateway;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BlockIoIPN::class,
        UpdateBadgeCron::class,
        MainCron::class,
        BinanceCron::class,
        PayoutCurrencyUpdateCron::class,
        PayoutCryptoCurrencyUpdateCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $basicControl = Configure::first();
        $blockIoGateway = Gateway::where(['code' => 'blockio', 'status' => 1])->count();
        if ($blockIoGateway == 1) {
            $schedule->command('blockIo:ipn')->everyThirtyMinutes();
        }

        $schedule->command('binance-payout-status:update')
            ->everyFiveMinutes();

        if ($basicControl->currency_layer_auto_update == 1) {
            $schedule->command('payout-currency:update')
                ->{$basicControl->currency_layer_auto_update_at}();
        }
        if ($basicControl->coin_market_cap_auto_update == 1) {
            $schedule->command('payout-crypto-currency:update')->{$basicControl->coin_market_cap_auto_update_at}();
        }


        $schedule->command('badge:cron')->daily();

        $schedule->command('main:cron')->daily();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
