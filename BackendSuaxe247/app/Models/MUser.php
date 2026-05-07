<?php

namespace App\Models;

use CodeIgniter\Model;

class MUser extends Model
{
    protected $table = 'User'; // Tên bảng
    protected $primaryKey = 'PK_idUser'; // Khóa chính
    protected $useAutoIncrement = true; // Khóa chính tự tăng

    protected $allowedFields = [
        'fullName',
        'email',
        'phone',
        'password',
        'gender',
        'birthDate',
        'address',
        'FK_idRole',
        'FK_idStore',
        'created',
        'updated',
        'deleted'
    ];

    // Tự động cập nhật created, updated
    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';

    // Không dùng soft delete mặc định của CI4, mà quản lý deleted = 0/1 thủ công
    protected $deletedField = 'deleted';

    /**
     * Lấy tất cả user
     */
    public function getAllUser()
    {
        return $this
        // ->where('deleted', 0)
        ->findAll();
    }

    /**
     * Lấy tất cả nhân viên (trừ KH, GĐ).
     */
    public function getAllStaff()
{
    return $this->select('user.PK_idUser, user.fullName, user.email, user.phone, user.gender, user.birthDate, user.address, user.FK_idRole, user.FK_idStore, role.roleName, store.address as storeAddress')
        ->join('role', 'role.PK_idRole = user.FK_idRole')
        ->join('store', 'store.PK_idStore = user.FK_idStore', 'left')
        ->whereNotIn('role.roleName', ['Giám đốc', 'Khách hàng'])
        ->where('user.deleted', 0)
        ->findAll();
}


    /**
     * Thêm user mới.
     */
    public function addUser($data)
    {
        $data['deleted'] = 0;

        $conflict = [];

        // Kiểm tra trùng email với user chưa bị xóa
        $emailExists = $this->where('email', $data['email'])
                            ->where('deleted', 0)
                            ->first();
        if ($emailExists) $conflict[] = 'Email';

        // Kiểm tra trùng phone với user chưa bị xóa
        $phoneExists = $this->where('phone', $data['phone'])
                            ->where('deleted', 0)
                            ->first();
        if ($phoneExists) $conflict[] = 'Số điện thoại';

        if (!empty($conflict)) {
            return ['conflict' => $conflict];
        }

        $insertId = $this->insert($data);
        return ['id' => $insertId];
    }

    public function updateUser($id, $data)
    {
        $conflict = [];

        // Kiểm tra trùng email
        $emailExists = $this->where('PK_idUser !=', $id)
                            ->where('email', $data['email'])
                            ->where('deleted', 0)
                            ->first();
        if ($emailExists) $conflict[] = 'Email';

        // Kiểm tra trùng số điện thoại
        $phoneExists = $this->where('PK_idUser !=', $id)
                            ->where('phone', $data['phone'])
                            ->where('deleted', 0)
                            ->first();
        if ($phoneExists) $conflict[] = 'Số điện thoại';

        if (!empty($conflict)) {
            return ['conflict' => $conflict];
        }

        // Nếu có mật khẩu mới thì mã hóa
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Nếu không có mật khẩu mới, loại bỏ key password tránh ghi đè null
            unset($data['password']);
        }

        $updated = $this->update($id, $data);
        return ['updated' => $updated];
    }




    /**
     * Xóa mềm user (deleted = 1).
     */
    public function deleteUser($id)
    {
        return $this->update($id, ['deleted' => 1]);
    }

    /**
     * Lấy chi tiết user theo ID.
     */
    public function getStaffById($id)
    {
        return $this->select('User.*, Role.roleName, Store.address as storeAddress')
            ->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
            ->join('Store', 'Store.PK_idStore = User.FK_idStore', 'left')
            ->where('User.deleted', 0)
            ->where('User.PK_idUser', $id)
            ->first();
    }

    public function getFilteredStaff($name = null, $gender = null, $roleId = null, $storeId = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        $builder->select('User.*, Role.roleName, Store.address AS storeAddress');
        $builder->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left');
        $builder->join('Store', 'Store.PK_idStore = User.FK_idStore', 'left');
        $builder->where('User.deleted', 0);

        // Loại bỏ Admin và Khách hàng
        $builder->whereNotIn('Role.roleName', ['Admin', 'Khách hàng']);

        if (!empty($name)) {
            $builder->like('User.fullName', $name);
        }

        if ($gender !== null && $gender !== '') {
            $builder->where('User.gender', $gender);
        }

        if ($roleId !== null && $roleId !== '') {
            $builder->where('User.FK_idRole', $roleId);
        }

        // Nếu có storeId (khi là Quản lý cửa hàng) → lọc theo cửa hàng
        if ($storeId !== null && $storeId !== '' && $storeId !== 'all') {
            $builder->where('User.FK_idStore', $storeId);
        }

        // Tổng số record
        $total = $builder->countAllResults(false);

        // Giới hạn và trả kết quả
        $builder->limit($limit, $offset);
        return [
            'data'  => $builder->get()->getResultArray(),
            'total' => $total
        ];
    }


    /**
     * Lấy user để đăng nhập (bằng email hoặc số điện thoại và password).
     */
    public function getUser_Login($identifier)
    {
        return $this->select('User.*, Role.roleName, Store.address as storeAddress')
            ->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
            ->join('Store', 'Store.PK_idStore = User.FK_idStore', 'left')
            ->groupStart()
                ->where('User.email', $identifier)
                ->orWhere('User.phone', $identifier)
            ->groupEnd()
            ->where('User.deleted', 0)
            ->first();
    }

    public function registerCustomer($data)
    {
        $phone = $data['phone'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $fullName = $data['fullName'];

        $conflict = [];

        // Kiểm tra trùng email (chỉ với deleted = 0)
        $emailExists = $this->where('deleted', 0)
                            ->where('email', $email)
                            ->first();
        if ($emailExists) $conflict[] = 'Email';

        // Kiểm tra trùng phone (chỉ với deleted = 0)
        $phoneExists = $this->where('deleted', 0)
                            ->where('phone', $phone)
                            ->first();
        if ($phoneExists) $conflict[] = 'Số điện thoại';

        // Nếu có conflict, trả về danh sách
        if (!empty($conflict)) {
            return ['conflict' => $conflict];
        }

        // Nếu chưa tồn tại => thêm mới
        $insertData = [
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'FK_idRole' => 4, // role Khách hàng
            'deleted' => 0,
        ];
        $insertId = $this->insert($insertData);
        return ['id' => $insertId];
    }



    /**
     * Lấy danh sách kỹ thuật viên của một cửa hàng (dựa vào ID cửa hàng).
     */
    public function getTechniciansByStore($storeId)
    {
        return $this->select('User.PK_idUser, User.fullName')
                    ->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
                    ->where('User.FK_idStore', $storeId)
                    ->where('User.deleted', 0)
                    ->where('Role.roleName', 'Kỹ thuật viên')
                    ->findAll();
    }

    public function getUserByPhone($phone)
    {
        return $this->select('PK_idUser, fullName, phone, Role.roleName')
                    ->join('Role', 'Role.PK_idRole = User.FK_idRole')
                    ->where('phone', $phone)
                    ->where('Role.roleName !=', 'Admin')  // loại trừ Admin
                    ->where('User.deleted', 0)
                    ->first();
    }


    public function getFilteredCustomers($searchName = null, $searchPhone = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        
        $builder->select('User.PK_idUser, User.fullName, User.phone, User.email, User.gender, User.birthDate, User.address,
                        Role.roleName,
                        COUNT(DISTINCT Appointment.PK_idAppointment) as totalAppointments,
                        COUNT(DISTINCT Invoice.PK_idInvoice) as totalInvoices');
        
        $builder->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left');
        $builder->join('Appointment', 'Appointment.FK_idCustomer = User.PK_idUser AND Appointment.deleted = 0', 'left');
        $builder->join('Invoice', 'Invoice.FK_idCustomer = User.PK_idUser AND Invoice.deleted = 0', 'left');

        $builder->where('User.FK_idStore', null);
        $builder->where('Role.roleName', 'Khách hàng');
        $builder->where('User.deleted', 0);

        // Tách filter theo tên
        if (!empty($searchName)) {
            $builder->like('User.fullName', $searchName);
        }

        // Tách filter theo số điện thoại
        if (!empty($searchPhone)) {
            $builder->like('User.phone', $searchPhone);
        }

        $builder->groupBy('User.PK_idUser, User.fullName, User.phone, User.email, User.gender, User.birthDate, User.address, Role.roleName');

        // Tổng số record
        $total = $builder->countAllResults(false);

        // Giới hạn và phân trang
        $builder->limit($limit, $offset);

        return [
            'data'  => $builder->get()->getResultArray(),
            'total' => $total
        ];
    }


    public function getCustomerDetail($customerId)
    {
        $builder = $this->builder();
        
        $builder->select('User.PK_idUser, User.fullName, User.phone, User.email, User.gender, User.birthDate, User.address,
                        Role.roleName,
                        COUNT(DISTINCT Appointment.PK_idAppointment) as totalAppointments,
                        COUNT(DISTINCT Invoice.PK_idInvoice) as totalInvoices');
        
        $builder->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left');
        $builder->join('Appointment', 'Appointment.FK_idCustomer = User.PK_idUser AND Appointment.deleted = 0', 'left');
        $builder->join('Invoice', 'Invoice.FK_idCustomer = User.PK_idUser AND Invoice.deleted = 0', 'left');
        
        // Chỉ khách hàng, chưa xoá
        $builder->where('User.PK_idUser', $customerId);
        $builder->where('Role.roleName', 'Khách hàng');
        $builder->where('User.deleted', 0);
        
        // GROUP BY tất cả các cột non-aggregate
        $builder->groupBy('User.PK_idUser, User.fullName, User.phone, User.email, User.gender, User.birthDate, User.address, Role.roleName');
        
        return $builder->get()->getRowArray();
    }

    /**
     * Lấy thông tin người dùng hiện tại (dựa vào session)
     */
    public function getCurrentUser()
    {
        $session = session();
        $userSession = $session->get('user'); // lấy mảng 'user' thay vì 'user_id'

        if (!$userSession || !isset($userSession['user_id'])) {
            return null; // chưa đăng nhập
        }

        $userId = $userSession['user_id'];

        return $this->select('User.*, Role.roleName, Store.address as storeAddress')
                    ->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
                    ->join('Store', 'Store.PK_idStore = User.FK_idStore', 'left')
                    ->where('User.deleted', 0)
                    ->where('User.PK_idUser', $userId)
                    ->first();
    }

    /**
     * Cập nhật thông tin người dùng hiện tại
     */
    public function updateCurrentUser($userId, $data)
    {
        $allowed = ['fullName', 'email', 'phone', 'gender', 'birthDate', 'address'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) return false;

        $conflict = [];

        // Kiểm tra email trùng với người dùng khác (deleted = 0)
        if (!empty($updateData['email'])) {
            $emailExists = $this->where('PK_idUser !=', $userId)
                                ->where('email', $updateData['email'])
                                ->where('deleted', 0)
                                ->first();
            if ($emailExists) $conflict[] = 'Email';
        }

        // Kiểm tra phone trùng với người dùng khác (deleted = 0)
        if (!empty($updateData['phone'])) {
            $phoneExists = $this->where('PK_idUser !=', $userId)
                                ->where('phone', $updateData['phone'])
                                ->where('deleted', 0)
                                ->first();
            if ($phoneExists) $conflict[] = 'Số điện thoại';
        }

        if (!empty($conflict)) {
            return ['conflict' => $conflict]; // trả về cho controller xử lý
        }

        $updated = $this->update($userId, $updateData);
        return ['updated' => $updated];
    }



    /**
     * Đổi mật khẩu người dùng hiện tại
     */
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        $user = $this->find($userId);

        if (!$user) {
            return ['status' => 'error', 'message' => 'Không tìm thấy người dùng.'];
        }

        // Kiểm tra mật khẩu cũ
        if (!password_verify($oldPassword, $user['password'])) {
            return ['status' => 'error', 'message' => 'Mật khẩu cũ không đúng.'];
        }

        // Hash mật khẩu mới và lưu lại
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->update($userId, ['password' => $hashed]);

        return ['status' => 'success', 'message' => 'Đổi mật khẩu thành công.'];
    }

    public function resetCustomerPassword($id, $newPassword)
    {
        if (empty($newPassword)) {
            return ['status' => 'error', 'message' => 'Mật khẩu không được để trống'];
        }

        // Mã hóa mật khẩu
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $updated = $this->update($id, ['password' => $hashed]);
        if ($updated) {
            return ['status' => 'success', 'message' => 'Đặt lại mật khẩu thành công'];
        }

        return ['status' => 'error', 'message' => 'Không thể cập nhật mật khẩu'];
    }

public function countTechniciansByStore($storeId)
{
    return $this->join('Role', 'Role.PK_idRole = User.FK_idRole', 'left')
                ->where('User.FK_idStore', $storeId)
                ->where('User.deleted', 0)
                ->where('Role.roleName', 'Kỹ thuật viên')
                ->countAllResults();
}



}
