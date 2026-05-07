<?php

namespace App\Controllers;

use App\Models\MUser;
use CodeIgniter\RESTful\ResourceController;

class CStaff extends ResourceController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new MUser();
    }

    public function index()
    {
        $name   = $this->request->getVar('search');
        $gender = $this->request->getVar('gender');
        $roleId = $this->request->getVar('roleId');
        $storeId = $this->request->getVar('storeId'); // FE có thể truyền hoặc không

        // Lấy thông tin user hiện tại
        $currentUser = session()->get('user'); // hoặc decode từ token
        $currentRole = $currentUser['roleName'] ?? '';
        $currentStoreId = $currentUser['FK_idStore'] ?? null;

        // Phân trang
        $page  = (int)$this->request->getVar('page') ?: 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Nếu là Quản lý cửa hàng → chỉ xem nhân viên cửa hàng của họ
        if ($currentRole === 'Quản lý cửa hàng') {
            $storeId = $currentStoreId;
        }

        $result = $this->userModel->getFilteredStaff(
            $name,
            $gender,
            $roleId,
            $storeId,
            $limit,
            $offset
        );

        return $this->respond([
            'status'  => 'success',
            'data'    => $result['data'],
            'total'   => $result['total'],
            'page'    => $page,
            'perPage' => $limit,
            'message' => 'Danh sách nhân viên'
        ]);
    }




    public function show($id = null){
        $user = $this->userModel->getStaffById($id);
        if($user){
            return $this->respond([
                'status' => 'success',
                'data' => $user
            ]);
        }
        return $this->failNotFound('Không tìm thấy người dùng');
    }

    /**
     * Thêm nhân viên mới
     */
    public function create()
    {
        $userModel = new MUser();
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Dữ liệu gửi lên không hợp lệ'
            ], 400);
        }

        if (empty($data['FK_idRole'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Thiếu role của nhân viên'
            ], 400);
        }

        if (isset($data['FK_idStore']) && $data['FK_idStore'] === '') {
            $data['FK_idStore'] = null;
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $result = $userModel->addUser($data);

        if (isset($result['id'])) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Thêm nhân viên thành công',
                'id' => $result['id']
            ], 201);
        } else if (isset($result['conflict'])) {
            $message = implode(' và ', $result['conflict']) . ' đã tồn tại';
            return $this->respond([
                'status' => 'error',
                'message' => $message
            ], 409);
        }

        return $this->respond([
            'status' => 'error',
            'message' => 'Không thể thêm nhân viên'
        ], 500);
    }



    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$id || !$data) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Dữ liệu gửi lên không hợp lệ'
            ], 400);
        }

        // Nếu có password nhưng trống => loại bỏ
        if (isset($data['password']) && trim($data['password']) === '') {
            unset($data['password']);
        }

        try {
            $result = $this->userModel->updateUser($id, $data);

            if (isset($result['updated']) && $result['updated']) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Cập nhật nhân viên thành công',
                ]);
            } else if (isset($result['conflict'])) {
                $message = implode(' và ', $result['conflict']) . ' đã tồn tại';
                return $this->respond([
                    'status' => 'error',
                    'message' => $message
                ], 409);
            }

            return $this->respond([
                'status' => 'error',
                'message' => 'Không tìm thấy nhân viên'
            ], 404);

        } catch (Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Cập nhật nhân viên thất bại. Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }




    public function delete($id = null){
        try {
            if ($this->userModel->deleteUser($id)) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Xóa nhân viên thành công',
                ]);
            }
        } catch (Exception $e) {
            return $this->fail('Xóa nhân viên thất bại. Lỗi: ' . $e->getMessage(), 500);
        }

        return $this->failNotFound('Không tìm thấy nhân viên.');
    }

    public function getTechnicians()
    {
        $session = session();
        $user = $session->get('user');
        $storeId = $user['store'] ?? null;

        if (!$storeId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Không tìm thấy thông tin cửa hàng trong session'
            ], 400);
        }

        $technicians = $this->userModel->getTechniciansByStore($storeId);

        if (!$technicians || count($technicians) === 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Không tìm thấy kỹ thuật viên trong cửa hàng này'
            ], 404);
        }

        return $this->respond([
            'status' => 'success',
            'data' => $technicians
        ]);
    }

     public function countTechniciansByStore($storeId = null)
    {
        if (!$storeId) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Thiếu ID cửa hàng'
            ], 400);
        }

        try {
            $count = $this->userModel->countTechniciansByStore($storeId);

            return $this->respond([
                'status' => 'success',
                'storeId' => $storeId,
                'count' => $count,
                'message' => 'Số lượng kỹ thuật viên trong cửa hàng'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Không thể lấy dữ liệu. Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }


}