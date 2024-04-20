<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LEDController;

class LEDUpdateVariable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'led';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'led description';

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
        $controller = new LEDController();
        $controller->updateVariable();
    }
}
