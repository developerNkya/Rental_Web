<?php
namespace App\Controllers;

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
    
        
        $tenants_today = $tenantModel
            ->where('rent_deadline', $today)
            ->groupStart()
                ->where('notified_at IS NULL')
                ->orWhere('notified_at !=', $today)
            ->groupEnd()
            ->findAll();
    
        
        $tenants_week = $tenantModel
            ->where('rent_deadline', $one_week_from_now)
            ->groupStart()
                ->where('notified_at IS NULL')
                ->orWhere('notified_at !=', $one_week_from_now)
            ->groupEnd()
            ->findAll();
    
        
            foreach ($tenants_today as $tenant) {
                $user = $userModel->find($tenant['user_id']);
                $language_id = $user['language_id'] ?? 1;
            
                $message = ($language_id == 2)
                    ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako imeisha, tafadhali lipa kwa wakati, ahsante."
                    : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due today, kindly pay on time, thank you!";
            
            $notificationModel->addToQueue($tenant['id'],$tenant['phone_number'], $message);
            $tenantModel->update($tenant['id'], ['notified_at' => $today]);
        }
    
        
        foreach ($tenants_week as $tenant) {
            
            $user = $userModel->find($tenant['user_id']);
            $language_id = $user['language_id'] ?? 1;      
            $message = ($language_id == 2)
            ? "Habari {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, unakumbushwa kua kodi yako itaisha baada ya wiki moja, tafadhali lipa kwa wakati, ahsante."
            : "Hello {$tenant['first_name']} {$tenant['middle_name']} {$tenant['last_name']}, you are reminded that your rent is due after a week, kindly pay on time, thank you!"; 

            $notificationModel->addToQueue($tenant['id'],$tenant['phone_number'], $message);
            $tenantModel->update($tenant['id'], ['notified_at' => $one_week_from_now]);
        }
    
        echo "Notifications added to the queue.";
    }
    
    
    
}

