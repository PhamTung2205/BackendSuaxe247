<?php

namespace App\Models;

use CodeIgniter\Model;

class MRole extends Model
{
    protected $table = 'Role'; // Tên bảng
    protected $primaryKey = 'PK_idRole'; // Khóa chính
    protected $allowedFields = ['PK_idRole', 'roleName']; // Các cột được phép thao tác

    public function getAllRole()
    {
        return $this->findAll(); // Lấy tất cả dữ liệu
    }

    public function getStaffRoles()
    {
        return $this->whereNotIn('roleName', ['Admin', 'Khách hàng'])
                    ->findAll();
    }
}
