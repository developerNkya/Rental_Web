<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Language;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class LanguageController extends BaseController
{
    public function updateUserLanguage()
    {
        $user_id = $this->request->getPost('user_id');
        $phone_number = $this->request->getPost('phone_number');
        $language_id = $this->request->getPost('language_id');


        try {
            if (empty($user_id) || empty($phone_number) || empty($language_id)
            ) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kindly fill all fields!',
                ]);
            }


            $userModel = new User();
            $currentData = $userModel
                ->where('id', $user_id)
                ->where('phone_number', $phone_number)
                ->set([
                    "language_id"=>$language_id
                ])
                ->update();


            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lnaguage Updated Successfully!',
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
