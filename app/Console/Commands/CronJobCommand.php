<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronJobController;

class CronJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plant sites description';

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
        $controller = new CronJobController();
        $controller->index();
    }
}
