<?php

namespace App\Models;

use CodeIgniter\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'first_name', 'middle_name', 'last_name', 'phone_number', 'role_id', 
        'rental_months', 'rental_price', 'owner_id', 'collection_id','rent_contract',
        'rent_deadline', 'electricity_units_from', 'electricity_units_to', 
        'notified_at', 'user_id'
    ];
    protected $returnType = 'array';

    public function getUserLanguage($userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        return $builder->select('language_id')->where('id', $userId)->get()->getRowArray();
    }
}

