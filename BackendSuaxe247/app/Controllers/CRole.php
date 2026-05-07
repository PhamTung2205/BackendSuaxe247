<?php

namespace App\Controllers;

use App\Models\MRole;
use CodeIgniter\RESTful\ResourceController;

class CRole extends ResourceController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new MRole();
    }

    // Lấy tất cả role
    public function index()
    {
        $roles = $this->roleModel->getAllRole();

        return $this->respond([
            'status'  => 'success',
            'data'    => $roles,
            'message' => 'Danh sách tất cả quyền'
        ]);
    }

    // Lấy role nhân viên (trừ Giám đốc và Khách hàng)
    public function staffRoles()
    {
        $roles = $this->roleModel->getStaffRoles();

        return $this->respond([
            'status'  => 'success',
            'data'    => $roles,
            'message' => 'Danh sách quyền nhân viên'
        ]);
    }
}
