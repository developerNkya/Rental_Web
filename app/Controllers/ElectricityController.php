<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Tenant;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class ElectricityController extends BaseController
{
    public function index()
    {
        //
    }

    public function updateElectricity()
    {
        $tenant_id = $this->request->getPost('tenant_id');
        $owner_id = $this->request->getPost('owner_id');
        $units_from = $this->request->getPost('units_from');
        $units_to = $this->request->getPost('units_to');

        try {
            if (empty($tenant_id) || empty($units_from) || empty($units_to) || empty($owner_id)
            ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly fill all fields!',
                ]);
            }


            $userModel = new Tenant();
            $currentData = $userModel
                ->select('electricity_units_from, electricity_units_to')
                ->where('id', $tenant_id)
                ->where('owner_id', $owner_id)
                ->first();
            
            if ($currentData) {
                if (
                    $currentData['electricity_units_from'] == $units_from &&
                    $currentData['electricity_units_to'] == $units_to
                ) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No changes detected. Update not performed.',
                    ]);
                }
            }
            
            $updatedElectricity = $userModel
                ->where('id', $tenant_id)
                ->where('owner_id', $owner_id)
                ->set([
                    'electricity_units_from' => $units_from,
                    'electricity_units_to' => $units_to,
                ])
                ->update();
            

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Electricity Bill Updated Successfully!',
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
