<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Collection;
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
                'username' => $phoneNumber, //generate_username_by_role($role_id),
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

    
    public function addCollection()
    {
        $name = $this->request->getPost('name');
        $location = $this->request->getPost('location');
        $owner_id = $this->request->getPost('owner_id');
    
        try {
            if (empty($name) || empty($location) || empty($owner_id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly fill all fields!',
                ]);
            }

            $collection = new Collection();    
            $existingCollection = $collection
                ->where('name', $name)
                ->where('owner_id', $owner_id)
                ->first();
    
            if ($existingCollection) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'A collection with the same name already exists for this owner.',
                ]);
            }

            $collection->insert([
                'name' => $name,
                'location' => $location,
                'owner_id' => $owner_id,
            ]);
    
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Collection added successfully!',
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

    public function fetchCollection()
    {
        try {
            $collectionModel = new Collection();
            $page = $this->request->getVar('page') ?? 1;
            $owner_id = $this->request->getVar('owner_id');
            $perPage = 5;


            $owners = $collectionModel->where('owner_id', $owner_id)->paginate($perPage, 'default', $page);
            $pager = $collectionModel->pager;

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
