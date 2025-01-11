<?php

namespace App\Database\Seeds;

use App\Models\Language;
use CodeIgniter\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        $roleModel = new Role();
        $userModel = new User();
        $languageModel = new Language(); // Add Language model
    
        // Get the database connection
        $db = \Config\Database::connect();
    
        // Disable foreign key checks
        $db->query('SET foreign_key_checks = 0');
    
        // First, clear existing roles to start fresh
        $db->table('roles')->truncate(); // Truncate the roles table
    
        echo "Existing roles deleted successfully.\n";
    
        // Enable foreign key checks again
        $db->query('SET foreign_key_checks = 1');
    
        // Define roles and corresponding IDs
        $roles = [
            1 => 'admin',
            2 => 'landlord',
            3 => 'tenant'
        ];
    
        // Insert roles with explicit IDs
        foreach ($roles as $id => $roleName) {
            $roleModel->insert([
                'id'   => $id, 
                'name' => $roleName
            ]);
        }
    
        echo "Roles created successfully with IDs 1, 2, 3.\n";
    
        // Check if admin user already exists
        if ($userModel->where('first_name', 'admin')->first()) {
            echo "Admin user already exists.\n";
        } else {
            // Get admin role ID (which is now guaranteed to exist with ID 1)
            $adminRole = $roleModel->where('name', 'admin')->first();
            if (!$adminRole) {
                echo "Admin role not found.\n";
            } else {
                // Insert admin user with correct role_id
                $userModel->insert([
                    'first_name'   => 'Admin',
                    'middle_name'  => 'System',
                    'status'  => 'Active',
                    'last_name'    => 'Administrator',
                    'password'     => password_hash('admin', PASSWORD_DEFAULT), // Hashed password
                    'phone_number' => '1234567890',
                    'role_id'      => 1, // Use actual admin role ID
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
    
                echo "Admin user created successfully.\n";
            }
        }
    
        // Check for existing languages
        $languages = ['English', 'Swahili'];
        foreach ($languages as $language) {
            if (!$languageModel->where('name', $language)->first()) {
                // Insert language if it doesn't exist
                $languageModel->insert([
                    'name'        => $language,
                ]);
    
                echo "Language '$language' added successfully.\n";
            } else {
                echo "Language '$language' already exists.\n";
            }
        }
    }
    
}
