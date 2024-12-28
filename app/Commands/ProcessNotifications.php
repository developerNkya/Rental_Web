<?php

namespace App\Commands;

use App\Constants;
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
                $phone = $jobData['tenant_phone'];         
                $this->sendNotification($tenantId, $phone,$message);
            } else {               
                CLI::write("No jobs in the queue, waiting...", 'yellow');
                sleep(2);  
            }
        }
    }

    private function sendNotification($tenantId,$phone, $message)
    {        
        CLI::write("Sending notification to tenant ID $tenantId: $message", 'blue');
        CLI::write("phone: : $phone", 'blue');
        CLI::write("message: $message", 'blue');


        $username = Constants::NEXT_USERNAME;
        $password = Constants::NEXT_PASSWORD;
    
        $postData = [
            'from' => 'SCHOOL',
            'to' => $phone,
            'text' => $message
        ];
    
        $url = 'https://messaging-service.co.tz/api/sms/v1/text/single';
    
    
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode("$username:$password")
            ),
        ));
    
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            CLI::write("Failed to send SMS to {$this->phone}: " . curl_error($curl), 'red');
            // \log::error("Failed to send SMS to {$this->phone}: " . curl_error($curl));
        } else {
            CLI::write("SMS sent successfully to {$this->phone}: " . $response, 'green');
            // \Log::info("SMS sent successfully to {$this->phone}: " . $response);
        }
        curl_close($curl);
    }




}

