<?php
namespace App\Controllers;

use App\Commands\ProcessRentNotifications;
use App\Models\Tenant;
use App\Models\TenantModel;
use App\Models\NotificationModel;
use App\Models\User;

class NotificationController extends BaseController
{
    public function sendRentNotifications()
    {
        $tenantModel = new Tenant();
        $userModel = new User();
        $notificationModel = new NotificationModel();
    
        $today = date('Y-m-d');
        $one_week_from_now = date('Y-m-d', strtotime('+1 week'));
    
        // Get tenants whose rent is due today
        $tenants_today = $tenantModel
            ->where('rent_deadline', $today)
            ->groupStart()
                ->where('notified_at IS NULL')
                ->orWhere('notified_at !=', $today)
            ->groupEnd()
            ->findAll();
    
        // Get tenants whose rent is due in one week
        $tenants_week = $tenantModel
            ->where('rent_deadline', $one_week_from_now)
            ->groupStart()
                ->where('notified_at IS NULL')
                ->orWhere('notified_at !=', $one_week_from_now)
            ->groupEnd()
            ->findAll();
    
        // Process tenants with rent due today
        foreach ($tenants_today as $tenant) {
            $user = $userModel->find($tenant['user_id']);
            $language_id = $user['language_id'] ?? 1;
    
            $message = ($language_id == 2)
                ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako imeisha, tafadhali lipa kwa wakati, ahsante."
                : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due today, kindly pay on time, thank you!";
    
            $notificationModel->addToQueue($tenant['id'], $tenant['phone_number'], $message);
    
            // Calculate the next rent deadline
            $n = (int) $tenant['rental_months']; // Assuming 'rental_months' is the column name for 'n'
            $current_deadline = new \DateTime($tenant['rent_deadline']);
            $next_deadline = $current_deadline->modify("+$n months")->format('Y-m-d');
    
            // Update the tenant's notified_at and rent_deadline columns
            $tenantModel->update($tenant['id'], [
                'notified_at' => $today,
                'rent_deadline' => $next_deadline
            ]);
        }
    
        // Process tenants with rent due in one week
        foreach ($tenants_week as $tenant) {
            $user = $userModel->find($tenant['user_id']);
            $language_id = $user['language_id'] ?? 1;
    
            $message = ($language_id == 2)
                ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako itaisha baada ya wiki moja, tafadhali lipa kwa wakati, ahsante."
                : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due after a week, kindly pay on time, thank you!";
    
            $notificationModel->addToQueue($tenant['id'], $tenant['phone_number'], $message);
    
            // Update the tenant's notified_at column
            $tenantModel->update($tenant['id'], [
                'notified_at' => $one_week_from_now
            ]);
        }
    
        echo "Notifications added to the queue.";
    }
    
    
    public function triggerRentNotifications()
    {
        try {
            // Load the necessary models
            $tenantModel = new Tenant();
            $userModel = new User();
            
            try {
                $today = date('Y-m-d');
                $one_week_from_now = date('Y-m-d', strtotime('+1 week'));
            
                // Get tenants whose rent is due today
                $tenants_today = $tenantModel
                    ->where('rent_deadline', $today)
                    ->groupStart()
                        ->where('notified_at IS NULL')
                        ->orWhere('notified_at !=', $today)
                    ->groupEnd()
                    ->findAll();
            
                // Get tenants whose rent is due in one week
                $tenants_week = $tenantModel
                    ->where('rent_deadline', $one_week_from_now)
                    ->groupStart()
                        ->where('notified_at IS NULL')
                        ->orWhere('notified_at !=', $one_week_from_now)
                    ->groupEnd()
                    ->findAll();
            
                // Process tenants with rent due today
                foreach ($tenants_today as $tenant) {
                    try {
                        $this->sendNotification($tenant, $today, 'today');
                        $n = (int)$tenant['rental_months'];
                        $current_deadline = new \DateTime($tenant['rent_deadline']);
                        $next_deadline = $current_deadline->modify("+$n months")->format('Y-m-d');
            
                        // Update tenant details
                        $tenantModel->update($tenant['id'], [
                            'notified_at' => $today,
                            'rent_deadline' => $next_deadline,
                        ]);
                    } catch (\Exception $e) {
                        log_message('error', "Error processing tenant ID {$tenant['id']} for today: " . $e->getMessage());
                    }
                }
            
                // Process tenants with rent due in one week
                foreach ($tenants_week as $tenant) {
                    try {
                        $this->sendNotification($tenant, $one_week_from_now, 'week');
                        $tenantModel->update($tenant['id'], [
                            'notified_at' => $one_week_from_now,
                        ]);
                    } catch (\Exception $e) {
                        log_message('error', "Error processing tenant ID {$tenant['id']} for one week reminder: " . $e->getMessage());
                    }
                }
    
                // Return a response
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Rent notifications have been processed and sent.'
                ]);
            
            } catch (\Exception $e) {
                log_message('error', 'Error occurred while processing rent notifications: ' . $e->getMessage());
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'An error occurred while processing notifications.'
                ]);
            }
    
        } catch (\Exception $e) {
            // Handle any errors in the outer try block
            log_message('error', 'Unexpected error occurred: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred while processing the request.'
            ]);
        }
    }
    
    private function sendNotification($tenant, $notified_date, $type)
    {
        try {
            $userModel = new User();
            $user = $userModel->find($tenant['user_id']);
            $language_id = $user['language_id'] ?? 1;
        
            $message = ($type === 'today')
                ? (($language_id == 2)
                    ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako imeisha, tafadhali lipa kwa wakati, ahsante."
                    : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due today, kindly pay on time, thank you!")
                : (($language_id == 2)
                    ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako itaisha baada ya wiki moja, tafadhali lipa kwa wakati, ahsante."
                    : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due after a week, kindly pay on time, thank you!");
        
            $this->sendSMS($tenant['phone_number'], $message);
        } catch (\Exception $e) {
            log_message('error', 'Error sending notification for tenant ID ' . $tenant['id'] . ': ' . $e->getMessage());
        }
    }
    
    private function sendSMS($phone, $message)
    {
        try {
            $phone = preg_replace('/\D/', '', $phone);
        
            // If the phone number starts with 0, replace it with '255'
            if (substr($phone, 0, 1) == '0') {
                $phone = '255' . substr($phone, 1);
            }
        
            // If the phone number does not start with '255', ensure it does
            if (substr($phone, 0, 3) != '255') {
                $phone = '255' . $phone; // Prepend '255' if missing
            }
        
            $username = Constants::NEXT_USERNAME;
            $password = Constants::NEXT_PASSWORD;
        
            $postData = [
                'from' => 'SCHOOL',
                'to' => $phone,
                'text' => $message,
            ];
        
            $url = 'https://messaging-service.co.tz/api/sms/v1/text/single';
        
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode("$username:$password"),
                ],
            ]);
        
            $response = curl_exec($curl);
            curl_close($curl);
        
            if ($response === false) {
                log_message('error', "Failed to send SMS to {$phone}.");
            } else {
                log_message('info', "SMS sent successfully to {$phone}: {$response}");
            }
        } catch (\Exception $e) {
            log_message('error', 'Error sending SMS: ' . $e->getMessage());
        }
    }
    
    
    
    
}

