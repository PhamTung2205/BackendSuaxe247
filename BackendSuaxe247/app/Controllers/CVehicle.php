<?php

namespace App\Controllers;

use App\Models\MVehicle;
use CodeIgniter\RESTful\ResourceController;

class CVehicle extends ResourceController
{
    protected $vehicleModel;

    public function __construct()
    {
        $this->vehicleModel = new MVehicle();
    }



    public function show($id = null)
    {
        if (!$id) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Thiếu ID xe'
            ], 400);
        }

        try {
            $vehicle = $this->vehicleModel->getVehicleById($id);

            if (!$vehicle) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Xe không tồn tại hoặc đã bị xóa'
                ], 404);
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $vehicle,
                'message' => 'Thông tin xe'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy thông tin xe: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * 🔹 Lấy danh sách xe theo ID người dùng
     * GET /vehicle/user/{id}
     */



    public function userVehicles($userId = null)
    {
        if (!$userId) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Thiếu ID người dùng'
            ], 400);
        }

        try {
            // 🔹 Lấy trang hiện tại (page) từ query string, mặc định là 1
            $page = (int)($this->request->getGet('page') ?? 1);
            $limit = 6;
            $offset = ($page - 1) * $limit;

            // 🔹 Gọi model để lấy danh sách xe có phân trang
            $result = $this->vehicleModel->getVehiclesByUserId($userId, $limit, $offset);

            return $this->respond([
                'status' => 'success',
                'data'   => $result['data'],
                'total'  => $result['total'],
                'page'   => $page,
                'perPage'=> $limit
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách xe: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * 🔹 Thêm xe mới cho người dùng
     * POST /vehicle/create
     */
    // 🔹 Thêm xe
    public function create()
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        $data = $this->request->getJSON(true);

        if (empty($data['licensePlate']) || empty($data['type'])) {
            return $this->failValidationErrors('Vui lòng nhập đầy đủ biển số và loại xe');
        }

        $insertData = [
            'FK_idUser'    => $user['user_id'],
            'licensePlate' => trim($data['licensePlate']),
            'type'         => trim($data['type']),
            'deleted'      => 0
        ];

        $vehicleId = $this->vehicleModel->addVehicle($insertData);

        return $this->respondCreated([
            'status'     => 'success',
            'message'    => 'Thêm xe thành công',
            'vehicle_id' => $vehicleId
        ]);
    }

    // 🔹 Cập nhật xe
    public function update($id = null)
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        if (!$id) {
            return $this->failValidationErrors('Thiếu ID xe');
        }

        $data = $this->request->getJSON(true);
        if (empty($data['licensePlate']) || empty($data['type'])) {
            return $this->failValidationErrors('Vui lòng nhập đầy đủ biển số và loại xe');
        }

        // Kiểm tra quyền sở hữu xe
        $vehicle = $this->vehicleModel->getVehicleByIdAndUser($id, $user['user_id']);
        if (!$vehicle) {
            return $this->failNotFound('Xe không tồn tại hoặc không thuộc quyền sở hữu');
        }

        $updateData = [
            'licensePlate' => trim($data['licensePlate']),
            'type'         => trim($data['type'])
        ];

        $this->vehicleModel->updateVehicle($id, $updateData);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Cập nhật xe thành công'
        ]);
    }


    /**
     * 🔹 Xóa xe (soft delete)
     * DELETE /vehicle/delete/{id}
     */
    public function delete($id = null)
    {
        $user = session()->get('user');
        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        if (!$id) {
            return $this->failValidationErrors('Thiếu ID xe');
        }

        // 🔹 Kiểm tra quyền sở hữu
        $vehicle = $this->vehicleModel->getVehicleByIdAndUser($id, $user['user_id']);
        if (!$vehicle) {
            return $this->failNotFound('Xe không tồn tại hoặc không thuộc quyền sở hữu');
        }

        // 🔹 Gọi model để xóa xe (model sẽ tự kiểm tra lịch hẹn)
        $result = $this->vehicleModel->deleteVehicle($id);

        if (isset($result['status']) && $result['status'] === 'error') {
            return $this->respond([
                'status'  => 'error',
                'message' => $result['message']
            ], 400);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => $result['message'] ?? 'Xóa xe thành công'
        ]);
    }
        /**
     * 🔹 Lấy toàn bộ xe của người dùng (không phân trang)
     * GET /vehicle/user/all/{id}
     */
    public function allUserVehicles($userId = null)
    {
        if (!$userId) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Thiếu ID người dùng'
            ], 400);
        }

        try {
            // 🔹 Gọi model để lấy tất cả xe của user (hàm mới bạn đã thêm trong MVehicle)
            $vehicles = $this->vehicleModel->getAllVehiclesByUserId($userId);

            return $this->respond([
                'status' => 'success',
                'data'   => $vehicles,
                'total'  => count($vehicles),
                'message'=> 'Lấy toàn bộ xe của người dùng thành công'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy toàn bộ xe: ' . $e->getMessage()
            ], 500);
        }
    }

}
