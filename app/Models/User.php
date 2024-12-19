<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['first_name', 'middle_name', 'last_name', 'phone_number', 'role_id', 'username', 'password',
    'language_id'
];
    protected $returnType = 'array';
}
