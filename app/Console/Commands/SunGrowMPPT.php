<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\HardwareAPIData\SunGrowMPPTController;

class SunGrowMPPT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sungrow:mppt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SunGrow MPPT Cron Job Controller';

    protected $commands = [
        'App\Console\Commands\RegisteredUsers',
    ];

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
     * @return mixed
     */
    public function handle()
    {
        $controller = new SunGrowMPPTController();
        $controller->sunGrow();
    }
}
