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


            if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 10) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly enter a valid 10-digit phone number.',
                ]);
            }

            $userModel = new User();
            $existingUser = $userModel->where('phone_number', $phoneNumber)->first();
            if ($existingUser) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This phone number is already registered.',
                ]);
            }


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
                'message' => 'User added successfully!',
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

    public function fetchOwners()
    {
        try {
            $userModel = new User();
            $page = $this->request->getVar('page') ?? 1;
            $perPage = 5;


            $owners = $userModel->where('role_id', 2)->paginate($perPage, 'default', $page);
            $pager = $userModel->pager;

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owners fetched successfully.',
                'data' => $owners,
                'pagination' => [
                    'currentPage' => $pager->getCurrentPage(),
                    'totalPages' => $pager->getPageCount(),
                    'perPage' => $pager->getPerPage(),
                    'totalItems' => $pager->getTotal(),
                ],
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
