<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Tenant;
use CodeIgniter\HTTP\ResponseInterface;

class TenantController extends BaseController
{
    public function fetchTenant()
    {
        try {
            $tenantModel = new Tenant();
            $user_id = $this->request->getVar('user_id');


            $tenants = $tenantModel->where('user_id', $user_id)->find();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Owners fetched successfully.',
                'data' => $tenants,
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

    public function fetchTenantContract()
    {
        try {
            $tenantModel = new Tenant();
            $user_id = $this->request->getVar('user_id');
        
            if (empty($user_id)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User ID is required.',
                ]);
            }
        
            // Fetch tenant contract
            $tenant = $tenantModel->where('user_id', $user_id)->first();
        
            if (!$tenant) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No tenant found for the given User ID.',
                ]);
            }
        
            if (empty($tenant['rent_contract'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No contract found for the tenant.',
                ]);
            }
        
            $contractPath = WRITEPATH . $tenant['rent_contract']; // Full path to the file
        
            if (!file_exists($contractPath)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contract file not found on the server.',
                ]);
            }
    
            // Set headers to indicate it's a downloadable file
            return $this->response
                ->download($contractPath, null)
                ->setContentType(mime_content_type($contractPath)) // Set correct content type
                ->setHeader('Content-Disposition', 'attachment; filename="' . basename($contractPath) . '"'); // Force download
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
