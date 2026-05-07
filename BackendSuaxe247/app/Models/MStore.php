<?php

namespace App\Models;

use CodeIgniter\Model;

class MStore extends Model
{
    protected $table = 'Store';
    protected $primaryKey = 'PK_idStore';
    protected $useAutoIncrement = false; // Nếu id tự tăng thì đổi thành true
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'PK_idStore',
        'address',
        'phone',
        'imageURL',
        'created',
        'updated',
        'deleted'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';
    protected $deletedField  = 'deleted';

    /**
     * Lấy tất cả cửa hàng chưa bị xóa
     */
    public function getAllStore()
    {
        return $this->where('deleted', 0)->findAll();
    }
      public function getAllStoreExceptKho()
    {
        return $this->where('deleted', 0)
                    ->where('PK_idStore !=', 'KHO')
                    ->findAll();
    }

    /**
     * Lấy cửa hàng theo ID
     */
    public function getStoreById($id)
    {
        return $this->where('PK_idStore', $id)
                    ->where('deleted', 0)
                    ->first();
    }

    /**
     * Thêm cửa hàng mới
     */
    public function addStore($data)
    {
        $data['deleted'] = 0;
        return $this->insert($data);
    }

    /**
     * Cập nhật cửa hàng
     */
    public function updateStore($id, $data)
    {
        $store = $this->find($id);
        if (!$store) {
            return false;
        }
        return $this->update($id, $data);
    }

    /**
     * Xóa mềm cửa hàng
     */
    public function deleteStore($id)
    {
        $store = $this->find($id);
        if (!$store) {
            return false;
        }
        return $this->update($id, ['deleted' => 1]);
    }
}
