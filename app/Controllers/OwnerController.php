<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Collection;
use App\Models\Tenant;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

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
                'status' =>'Active',
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


    public function addTenant()
    {
        $firstName = $this->request->getPost('first_name');
        $secondName = $this->request->getPost('second_name');
        $lastName = $this->request->getPost('last_name');
        $phoneNumber = $this->request->getPost('phone_number');
        $rentalMonths = $this->request->getPost('rental_months');
        $rentDeadline = $this->request->getPost('rent_deadline');
        $rentalPrice = $this->request->getPost('rental_price');
        $ownerId = $this->request->getPost('owner_id');
        $collectionId = $this->request->getPost('collection_id');
        $rentContract = $this->request->getFile('rent_contract');
    
        try {
            // Step 1: Validate required fields
            if (
                empty($firstName) || empty($lastName) || empty($phoneNumber) || empty($secondName) ||
                empty($rentalMonths) || empty($rentalPrice) || empty($ownerId) || empty($collectionId) ||
                empty($rentDeadline) || empty($rentContract)
            ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly fill all fields!',
                ]);
            }
    
            // Step 2: Validate phone number
            if (strlen($phoneNumber) !== 10) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly enter a valid 10-digit phone number.',
                ]);
            }
    
            // Step 3: Check for existing user or tenant with the same phone number
            $userModel = new User();
            $tenantModel = new Tenant();
    
            $existingUser = $userModel->where('phone_number', $phoneNumber)->first();
            $existingTenant = $tenantModel->where('phone_number', $phoneNumber)->first();
    
            if ($existingUser || $existingTenant) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This phone number is already registered.',
                ]);
            }
    
            // Step 4: Handle file upload
            $rentContractPath = null;
            if ($rentContract && $rentContract->isValid() && !$rentContract->hasMoved()) {
                $validMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                if (!in_array($rentContract->getMimeType(), $validMimeTypes)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid file type. Only PDF, JPEG, and PNG are allowed.',
                    ]);
                }
                if ($rentContract->getSize() > 2 * 1024 * 1024) { // 2MB limit
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'File size exceeds the 2MB limit.',
                    ]);
                }
    
                $uploadPath = WRITEPATH . 'uploads/rent_contracts/'; // Save in writeable directory
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true); // Create directory if it doesn't exist
                }
    
                $newName = $rentContract->getRandomName();
                $rentContract->move($uploadPath, $newName);
                $rentContractPath = 'uploads/rent_contracts/' . $newName; // Relative path
            }
    
            // Step 5: Insert user data
            helper('user');
            $roleId = 3; // Tenant role ID
    
            $userModel->insert([
                'first_name' => $firstName,
                'middle_name' => $secondName,
                'last_name' => $lastName,
                'username' => $phoneNumber,
                'password' => password_hash('tenant', PASSWORD_DEFAULT), // Default password
                'phone_number' => $phoneNumber,
                'role_id' => $roleId,
                'status' =>'Active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    
            $userId = $userModel->insertID();
    
            // Step 6: Insert tenant data
            $tenantModel->insert([
                'first_name' => $firstName,
                'middle_name' => $secondName,
                'last_name' => $lastName,
                'username' => $phoneNumber,
                'password' => password_hash('tenant', PASSWORD_DEFAULT), // Default password
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'role_id' => $roleId,
                'rental_months' => $rentalMonths,
                'rent_deadline' => $rentDeadline,
                'rental_price' => $rentalPrice,
                'owner_id' => $ownerId,
                'collection_id' => $collectionId,
                'rent_contract' => $rentContractPath, // Save relative path
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    
            // Step 7: Return success response
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tenant added successfully!',
            ]);
    
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            // Log the throwable error
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
    
    public function fetchTenants()
    {
        try {
            $tenantModel = new Tenant();
            $page = $this->request->getVar('page') ?? 1;
            $owner_id = $this->request->getVar('owner_id');
            $perPage = 5;


            $tenants = $tenantModel->where('owner_id', $owner_id)->paginate($perPage, 'default', $page);
            $pager = $tenantModel->pager;

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owners fetched successfully.',
                'data' => $tenants,
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
  
    public function ownerProfile()
    {
        try {
            $owner_id = $this->request->getVar('owner_id');
            $user = new User();
            $owner = $user 
                     ->where('id',$owner_id)
                     ->first();

            $cycle_day = $owner['cycle_tracker'];
            $today = new DateTime();
            $next_payment_date = new DateTime();

            $next_payment_date->setDate(
                $today->format('Y'),
                $today->format('m') + ($today->format('d') < $cycle_day ? 0 : 1),
                $cycle_day
            );
            

            if ($next_payment_date->format('d') != $cycle_day) {
                $next_payment_date->modify('last day of this month');
            }

            $tenantCount = (new Tenant())->where('owner_id', $owner_id)->countAllResults();
            $next_payment_amount = number_format($tenantCount * 500);
                     return $this->response->setJSON([
                        'success' => true,
                        'data' => [
                            'Fullname' => implode(' ', array_filter([$owner['first_name'], $owner['middle_name'], $owner['last_name']])),
                            'status' =>$owner['status'],
                            'next_payment_date' => $next_payment_date->format('Y-m-d'),
                            'next_payment_amount' => $next_payment_amount,
                            'deficity' => number_format($owner['deficity']) ?? 0

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

    public function deleteItem($id = null, $type = null)
    {
        try {
            // Validate input
            if (empty($id) || empty($type)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid parameters provided.',
                ]);
            }
    
            // Perform deletion based on the type
            switch ($type) {
                case 'tenants':
                    $tenant = new Tenant();
                    $deleted = $tenant->where('user_id', $id)->delete();
                    break;
    
                case 'collections':
                    $collection = new Collection();
                    $deleted = $collection->where('id', $id)->delete();
                    break;
    
                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid deletion type provided.',
                    ]);
            }
    
            if ($deleted) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => ucfirst($type) . ' successfully deleted.',
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => ucfirst($type) . ' could not be deleted or does not exist.',
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An internal server error occurred.',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    
    public function ownerSummary()
    {

        try {
            $owner_id = $this->request->getVar('owner_id');
    
            if (!$owner_id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Owner ID is required.',
                ]);
            }
    
            $tenant = new Tenant();

            $total_tenants = count($tenant->where('owner_id', $owner_id)->findAll());
    

            $monthly_tenants = count($tenant
                ->where('owner_id', $owner_id)
                ->where('rent_deadline >=', date('Y-m-01'))
                ->where('rent_deadline <=', date('Y-m-t'))
                ->findAll());
    
            $collectionModel = new Collection();
            $collections = count($collectionModel->where('owner_id', $owner_id)->findAll());

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owners Summary fetched successfully.',
                'data' => [
                    'total_tenants' => $total_tenants,
                    'monthly_tenants' => $monthly_tenants,
                    'collections' => $collections,
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
            $all = $this->request->getVar('all') ?? false;
            $page = $this->request->getVar('page') ?? 1;
            $owner_id = $this->request->getVar('owner_id');
            $perPage = 5;
            $collections = [];
            $pager = '';
    
            if ($all == false) {
                $collections = $collectionModel->where('owner_id', $owner_id)
                    ->paginate($perPage, 'default', $page);
                $pager = $collectionModel->pager;
            } else {
                $collections = $collectionModel->select('id, name')
                    ->where('owner_id', $owner_id)
                    ->findAll();
            }
    
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owners fetched successfully.',
                'data' => $collections,
                'pagination' => ($all == false) ? [
                    'currentPage' => $pager->getCurrentPage(),
                    'totalPages' => $pager->getPageCount(),
                    'perPage' => $pager->getPerPage(),
                    'totalItems' => $pager->getTotal(),
                ] : null,
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