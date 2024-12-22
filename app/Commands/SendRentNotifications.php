<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\NotificationController; 

class SendRentNotifications extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'sendRentNotifications';
    protected $description = 'Send rent notifications to tenants';

    public function run(array $params)
    {
        
        $controller = new NotificationController();
        $controller->sendRentNotifications();

        CLI::write('Rent notifications have been sent.', 'green');
    }
}
