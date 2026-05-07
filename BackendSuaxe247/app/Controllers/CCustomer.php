<?php

namespace App\Controllers;

use App\Models\MUser;
use CodeIgniter\RESTful\ResourceController;

class CCustomer extends ResourceController
{
    protected $format = 'json';
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new MUser();
    }

    /**
     * Đăng nhập (API)
     */
    // App/Controllers/CCustomer.php
    public function login()
    {
        $userModel = new MUser();
        $data = $this->request->getJSON(true);

        $identifier = $data['identifier'] ?? '';
        $password   = $data['password'] ?? '';

        $user = $userModel->getUser_Login($identifier);

        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Sai Email/Số điện thoại hoặc mật khẩu'], 401);
        }

        // kiểm tra password
        if (!password_verify($password, $user['password'])) {
            return $this->respond(['status' => 'error', 'message' => 'Sai Email/Số điện thoại hoặc mật khẩu'], 401);
        }

        // lưu session
        session()->set('user', [
            'user_id'      => $user['PK_idUser'],
            'fullName'     => $user['fullName'],
            'email'        => $user['email'],
            'phone'        => $user['phone'],
            'role'         => $user['FK_idRole'],
            'roleName'     => $user['roleName'],
            'store'        => $user['FK_idStore'],
            'storeAddress' => $user['storeAddress'],
        ]);
    session()->set('isLoggedIn', true);

        return $this->respond([
            'status' => 'success',
            'message' => 'Đăng nhập thành công',
            'user' => [
                'user_id'      => $user['PK_idUser'],
                'fullName'     => $user['fullName'],
                'email'        => $user['email'],
                'phone'        => $user['phone'],
                'role'         => $user['FK_idRole'],
                'roleName'     => $user['roleName'],
                'store'        => $user['FK_idStore'],
                'storeAddress' => $user['storeAddress'],
            ]
        ]);
    }


    /**
     * Đăng xuất
     */
    public function logout()
    {
        try {
            session()->destroy();
            
            return $this->respond([
                'status'  => 'success',
                'message' => 'Đăng xuất thành công!',
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi đăng xuất: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkSession()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        return $this->respond([
            'status' => 'success',
            'user'   => session()->get('user')
        ]);
    }

    public function register()
    {
        $userModel = new MUser();
        $data = $this->request->getJSON(true);

        // validate dữ liệu cơ bản
        if (empty($data['phone']) || empty($data['email']) || empty($data['password']) || empty($data['fullName'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Vui lòng nhập đầy đủ thông tin'
            ], 400);
        }

        // gọi model để xử lý
        $result = $userModel->registerCustomer($data);

        // Nếu có conflict (trùng email/phone)
        if (isset($result['conflict'])) {
            $message = implode(' và ', $result['conflict']) . ' đã tồn tại';
            return $this->respond([
                'status' => 'error',
                'message' => $message
            ], 409); // conflict
        }

        // Nếu thêm mới thành công
        if (isset($result['id'])) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Đăng ký thành công',
                'user_id' => $result['id']
            ], 201);
        }

        // Nếu có lỗi khác
        return $this->respond([
            'status' => 'error',
            'message' => 'Không thể đăng ký nhân viên'
        ], 500);
    }



    public function getByPhone($phone = null)
    {
        if (!$phone) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Thiếu số điện thoại'
            ], 400);
        }

        $customer = $this->userModel->getUserByPhone($phone);

        if (!$customer) {
            return $this->respond([
                'status' => 'not_found',
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $customer
        ]);
    }

    public function listCustomers()
    {
        $searchName  = $this->request->getVar('name');  // tìm theo tên
        $searchPhone = $this->request->getVar('phone'); // tìm theo số điện thoại
        $page        = (int)$this->request->getVar('page') ?: 1;
        $limit       = 10;
        $offset      = ($page - 1) * $limit;

        try {
            $result = $this->userModel->getFilteredCustomers($searchName, $searchPhone, $limit, $offset);

            return $this->respond([
                'status'  => 'success',
                'data'    => $result['data'],
                'total'   => $result['total'],
                'page'    => $page,
                'perPage' => $limit,
                'message' => 'Danh sách khách hàng'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerDetail($customerId = null)
    {
        if (!$customerId) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Thiếu ID khách hàng'
            ], 400);
        }

        try {
            $customer = $this->userModel->getCustomerDetail($customerId);

            if (!$customer) {
                return $this->respond([
                    'status'  => 'not_found',
                    'message' => 'Không tìm thấy khách hàng'
                ], 404);
            }

            return $this->respond([
                'status' => 'success',
                'data'   => $customer
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy chi tiết khách hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin người dùng hiện tại (theo session)
     */
    public function getCurrentUser()
    {
        // kiểm tra đăng nhập
        if (!session()->get('isLoggedIn')) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        // gọi model
        $user = $this->userModel->getCurrentUser();

        if (!$user) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $user
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân người dùng hiện tại
     */
    public function updateProfile()
    {
        $session = session();
        $userSession = $session->get('user');

        if (!$userSession || !isset($userSession['user_id'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $userId = $userSession['user_id'];
        $data = $this->request->getJSON(true);

        // Validate cơ bản
        if (empty($data['fullName']) || empty($data['email']) || empty($data['phone'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Họ tên, Email và Số điện thoại là bắt buộc.'
            ], 400);
        }

        $result = $this->userModel->updateCurrentUser($userId, $data);

        // Kiểm tra conflict từ model
        if (isset($result['conflict'])) {
            $message = implode(' và ', $result['conflict']) . ' đã tồn tại';
            return $this->respond([
                'status'  => 'error',
                'message' => $message
            ], 409);
        }

        // Cập nhật session nếu thành công
        if (isset($result['updated']) && $result['updated']) {

            // Lấy lại thông tin user mới từ DB
            $updatedUser = $this->userModel
                ->select('User.*, Role.roleName, Store.address as storeAddress')
                ->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
                ->join('Store', 'Store.PK_idStore = User.FK_idStore', 'left')
                ->where('User.PK_idUser', $userId)
                ->first();

            // Cập nhật lại session
            $session->set('user', [
                'user_id'      => $updatedUser['PK_idUser'],
                'fullName'     => $updatedUser['fullName'],
                'email'        => $updatedUser['email'],
                'phone'        => $updatedUser['phone'],
                'role'         => $updatedUser['FK_idRole'],
                'roleName'     => $updatedUser['roleName'],
                'store'        => $updatedUser['FK_idStore'],
                'storeAddress' => $updatedUser['storeAddress'],
            ]);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Cập nhật thông tin thành công.',
                'user'    => $session->get('user'), // 👈 FE có thể cập nhật lại state nếu cần
            ]);
        }

        return $this->respond([
            'status'  => 'error',
            'message' => 'Không có thông tin nào được cập nhật.'
        ], 400);
    }


    /**
     * Đổi mật khẩu người dùng hiện tại
     */
    public function changePassword()
    {
        $session = session();
        $userSession = $session->get('user');

        if (!$userSession || !isset($userSession['user_id'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $userId = $userSession['user_id'];
        $data = $this->request->getJSON(true);

        if (empty($data['oldPassword']) || empty($data['newPassword'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Vui lòng nhập đầy đủ mật khẩu cũ và mật khẩu mới.'
            ], 400);
        }

        $result = $this->userModel->changePassword($userId, $data['oldPassword'], $data['newPassword']);

        if ($result['status'] === 'success') {
            return $this->respond($result, 200);
        }

        return $this->respond($result, 400);
    }


    public function resetCustomerPassword($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$id || empty($data['password'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
        }

        try {
            $result = $this->userModel->resetCustomerPassword($id, $data['password']);

            if ($result['status'] === 'success') {
                return $this->respond($result, 200);
            }

            return $this->respond($result, 400);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

}
