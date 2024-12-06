<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\HTTP\Response;

class Login extends BaseController
{
    public function login()
    {
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username and password are required.',
            ]);
        }

        
        $userModel = new User();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) { 
            return $this->response->setJSON([
                'success' => true,
                'role'    => $user['role'],
            ]);
        }

        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid username or password.',
        ]);
    }
}
