<?php

namespace App\Console;

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
        Commands\Weather::class,
        Commands\SunGrow::class,
        Commands\SunGrowMPPT::class,
        Commands\CronJobCommand::class,
        Commands\PlantSiteData::class,
        Commands\PlantSite::class,
        Commands\Communication::class,
        Commands\LEDUpdateVariable::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:job')->withoutOverlapping();
        $schedule->command('sun:grow')->withoutOverlapping();
        $schedule->command('sungrow:mppt')->withoutOverlapping();
        $schedule->command('weather')->withoutOverlapping();
        // $schedule->command('plant:site')->withoutOverlapping();
        // $schedule->command('communication')->everyTenMinutes();
        // $schedule->command('led')->withoutOverlapping();
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
