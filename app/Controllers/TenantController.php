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
}
