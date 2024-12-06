<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AdminSeederController extends Controller
{
    public function initializeAdmin()
    {
        
        $seeder = \Config\Database::seeder();       
        $seeder->call('InitialAdminSeeder');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Admin user initialization attempted. Check the logs for details.',
        ]);
    }
}
