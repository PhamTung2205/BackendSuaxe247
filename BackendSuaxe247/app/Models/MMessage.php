<?php

namespace App\Models;

use CodeIgniter\Model;

class MMessage extends Model
{
    protected $table = 'Message'; // Tên bảng
    protected $primaryKey = 'PK_idMessage'; // Khóa chính
    protected $allowedFields = [
        'PK_idMessage',
        'FK_idCustomer',
        'FK_idStaff',
        'content',
        'created'
    ]; // Các cột được phép thao tác

    public function getAllMessage()
    {
        return $this->findAll(); // Lấy tất cả dữ liệu
    }
}
