<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class OwnerController extends BaseController
{
    public function addOwner()
    {

        $firstName = $this->request->getPost('first_name');
        $secondName = $this->request->getPost('second_name');
        $lastName = $this->request->getPost('last_name');
        $location = $this->request->getPost('location');
        $phoneNumber = $this->request->getPost('phone_number');

        try {
            if (empty($firstName) || empty($lastName) || empty($phoneNumber) || empty($secondName) || empty($location)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly fill all fields!',
                ]);
            }

            if(strlen($phoneNumber)){
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly enter a valid 10 digit number',
                ]);  
            }

            $userModel = new User();
            helper('user');
            $role_id = 2;
            $userModel->insert([
                'first_name' => $firstName,
                'middle_name' => $secondName,
                'last_name' => $lastName,
                'username' => generate_username_by_role($role_id),
                'password' => password_hash('owner', PASSWORD_DEFAULT),
                'phone_number' => $phoneNumber,
                'role_id' => $role_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User Added successfully!'
            ]);


        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An internal server error occurred.',
                'error' => $e->getMessage(),
            ]);
        }
    }

}
