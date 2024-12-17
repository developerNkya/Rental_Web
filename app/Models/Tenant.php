<?php

namespace App\Models;

use CodeIgniter\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $allowedFields = ['first_name', 'middle_name', 'last_name', 'phone_number', 'role_id', 
    'rental_months', 'rental_price','owner_id','collection_id','rent_deadline',
'electricity_units_from','electricity_units_to'
];
    protected $returnType = 'array';
}
