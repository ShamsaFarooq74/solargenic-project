<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\PlantsController;

class Weather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weather for all plant cities';

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
        $weatherController = new PlantsController();
        $weatherController->get_weather();
    }
}
