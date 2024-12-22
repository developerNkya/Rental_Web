<?php
namespace App\Controllers;

use App\Models\Tenant;
use App\Models\TenantModel;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    public function sendRentNotifications()
    {
        $tenantModel = new Tenant();
        $notificationModel = new NotificationModel();
    
        $today = date('Y-m-d');
        $one_week_from_now = date('Y-m-d', strtotime('+1 week'));
    
        $tenants_today = $tenantModel->where('rent_deadline', $today)->findAll();
        $tenants_week = $tenantModel->where('rent_deadline', $one_week_from_now)->findAll();
    
        foreach ($tenants_today as $tenant) {
            $message = "Your rent is due today, please pay it soon.";
            $notificationModel->addToQueue($tenant['id'], $message);
        }
    
        foreach ($tenants_week as $tenant) {
            $message = "Your rent will be due in one week.";
            $notificationModel->addToQueue($tenant['id'], $message);
        }
    
        echo "Notifications added to the queue.";
    }
    
}

