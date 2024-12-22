<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notification_queue';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tenant_id', 'message', 'status'];

    public function addToQueue($tenant_id, $message)
    {
        $redis = get_redis();
        
        $job = json_encode([
            'tenant_id' => $tenant_id,
            'message'   => $message,
        ]);
    
        
        $redis->rpush('notification_queue', $job);
    
        return true;
    }
    
}
