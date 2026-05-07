<?php

namespace App\Models;

use CodeIgniter\Model;

class MImportReceiptDetail extends Model
{
    protected $table = 'ImportReceiptDetail'; // Tên bảng
    protected $primaryKey = 'PK_idImportDetail'; // Khóa chính
    protected $allowedFields = [
        'PK_idImportDetail',
        'FK_idImport',
        'FK_idSparePart',
        'requestedQty',
        'importedQty'
    ]; // Các cột được phép thao tác

    public function getAllImportReceiptDetail()
    {
        return $this->findAll(); // Lấy tất cả dữ liệu
    }
}
