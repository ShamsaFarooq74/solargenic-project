<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\CommunicationController;

class Communication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'communication description';

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
        $controller = new CommunicationController();
        $controller->send_comm_email();
        $controller->send_comm_sms();
        $controller->send_comm_app_notification();
    }
}
