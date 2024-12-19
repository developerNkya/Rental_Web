<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\HTTP\Response;

class Login extends BaseController
{
    public function login()
    {
        try {
            $phone = $this->request->getPost('phone_number');
            $password = $this->request->getPost('password');
    
            if (empty($phone) || empty($password)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Phone number and password are required.',
                ]);
            }
    
            $userModel = new User();
            $user = $userModel->where('phone_number', $phone)->first();
    
            if ($user && password_verify($password, $user['password'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'role' => $user['role_id'],
                        'id' => $user['id'],
                        'language' => $user['language_id']?? 'N/A',
                        'phone_number' => $phone
                    ]
                ]);                
            }
    
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid username or password.',
            ]);
        } catch (\Throwable $e) { 
            log_message('error', $e->getMessage()); 
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An internal server error occurred.',
                'error'   => $e->getMessage(), 
            ]);
        }
    }
    
}
