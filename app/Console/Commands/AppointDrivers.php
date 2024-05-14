<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\OrderController;

class AppointDrivers extends Command
{
    protected $signature = 'appoint';

    protected $description = 'Назнание водителей';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $orderController = new OrderController();
        $orderController->appoint();
        $this->info('Водители назначены!');
    }
}
