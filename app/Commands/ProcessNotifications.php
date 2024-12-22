<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\Tenant;

class ProcessNotifications extends BaseCommand
{
    protected $group = 'Queue Worker';
    protected $name = 'process:notifications';
    protected $description = 'Process notification jobs from the Redis queue';

    public function run(array $params)
    {
        $redis = get_redis();       
        CLI::write("Starting notification processor...");

        while (true) {            
            $job = $redis->lpop('notification_queue');  
            if ($job) {
                $jobData = json_decode($job, true);
                $tenantId = $jobData['tenant_id'];
                $message = $jobData['message'];         
                $this->sendNotification($tenantId, $message);
            } else {               
                CLI::write("No jobs in the queue, waiting...", 'yellow');
                sleep(2);  
            }
        }
    }

    private function sendNotification($tenantId, $message)
    {        
        CLI::write("Sending notification to tenant ID $tenantId: $message", 'blue');
    }
}

