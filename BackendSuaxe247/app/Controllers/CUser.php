<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MUser;
use CodeIgniter\I18n\Time;

class CUser extends ResourceController
{
    use ResponseTrait;

    protected $modelName = MUser::class;
    protected $format    = 'json';

    /**
     * Lấy tất cả user (trừ deleted = 1)
     */
    public function index()
    {
        try {
            $users = $this->model->getAllUser();

            return $this->respond([
                'status'  => 'success',
                'data'    => $users,
                'message' => count($users) ? 'Danh sách người dùng' : 'Không có người dùng nào'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin user theo ID
     */
    public function show($id = null)
    {
        try {
            $user = $this->model->getStaffById($id);

            if (!$user) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $user,
                'message' => 'Thông tin người dùng'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy thông tin người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

   
}
