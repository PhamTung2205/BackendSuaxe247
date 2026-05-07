<?php

namespace App\Models;

use CodeIgniter\Model;

class MService extends Model
{
    protected $table = 'Service';
    protected $primaryKey = 'PK_idService';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'PK_idService',
        'serviceName',
        'description',
        'estimatedPrice',
        'estimatedTime',
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
     * 🔹 Lấy tất cả dịch vụ chưa bị xóa
     */
    public function getAllService()
    {
        return $this->where('deleted', 0)->findAll();
    }

    /**
     * 🔹 Lấy dịch vụ theo ID
     */
    public function getServiceById($id)
    {
        return $this->where('PK_idService', $id)
                    ->where('deleted', 0)
                    ->first();
    }

    /**
     * 🔹 Thêm dịch vụ mới
     */
    public function addService($data)
    {
        $data['deleted'] = 0;
        return $this->insert($data);
    }

    /**
     * 🔹 Cập nhật dịch vụ
     */
    public function updateService($id, $data)
    {
        $service = $this->find($id);
        if (!$service) return false;

        return $this->update($id, $data);
    }

    /**
     * 🔹 Xóa mềm dịch vụ
     */
    public function deleteService($id)
    {
        $service = $this->find($id);
        if (!$service) return false;

        return $this->update($id, ['deleted' => 1]);
    }
}
