<?php

namespace App\Models;

use CodeIgniter\Model;

class Tenant extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $allowedFields = ['first_name', 'middle_name', 'last_name', 'phone_number', 'role_id', 
    'rental_months', 'rental_price','owner_id','collection_id'];
    protected $returnType = 'array';
}
