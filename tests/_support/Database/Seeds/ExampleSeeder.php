<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        
        $exists = $builder->where('username', 'admin')->countAllResults();

        if ($exists > 0) {
            echo "Admin user already exists.\n";
            return;
        }

        
        $builder->insert([
            'first_name'   => 'Admin',
            'middle_name'  => 'System',
            'last_name'    => 'Administrator',
            'username'     => 'admin',
            'password'     => password_hash('admin', PASSWORD_DEFAULT), 
            'phone_number' => '1234567890',
            'role_id'      => 1, 
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        echo "Admin user created successfully.\n";
    }
}
