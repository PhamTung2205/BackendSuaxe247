<?php

namespace App\Models;

use CodeIgniter\Model;

class MStoreSparePart extends Model
{
    protected $table = 'Store_SparePart';
    protected $primaryKey = 'PK_idSSP';
    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'PK_idSSP',
        'FK_idStore',
        'FK_idSparePart',
        'stockQty',
        'warningQty',
        'location',
        'created',
        'updated',
        'deleted'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';
    protected $deletedField = 'deleted';

    /**
     * 🔹 Lấy toàn bộ phụ tùng còn hiệu lực
     */
    public function getAllStoreSpareParts()
    {
        return $this->where('deleted', 0)
                    ->orderBy('FK_idStore', 'ASC')
                    ->findAll();
    }

    /**
     * 🔹 Lấy phụ tùng theo cửa hàng
     */
    public function getByStore($storeId)
    {
        return $this->where('FK_idStore', $storeId)
                    ->where('deleted', 0)
                    ->orderBy('PK_idSSP', 'ASC')
                    ->findAll();
    }

    /**
     * 🔹 Lấy phụ tùng sắp hết hàng
     */
    public function getLowStockItems($storeId = null)
    {
        $builder = $this->where('deleted', 0)
                        ->where('stockQty <= warningQty');

        if ($storeId) {
            $builder->where('FK_idStore', $storeId);
        }

        return $builder->findAll();
    }

    /**
     * 🔹 Thêm 1 phụ tùng
     */
    public function addStoreSparePart($data)
    {
        $data['deleted'] = 0;
        return $this->insert($data);
    }

    /**
     * 🔹 Cập nhật phụ tùng
     */
    public function updateStoreSparePart($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * 🔹 Xóa mềm 1 phụ tùng
     */
    public function softDelete($id)
    {
        return $this->update($id, ['deleted' => 1]);
    }

    /**
     * 🔹 Xóa mềm toàn bộ phụ tùng của 1 cửa hàng
     */
    public function softDeleteByStore($storeId)
    {
        if (!$storeId) {
            return false;
        }

        return $this->where('FK_idStore', $storeId)
                    ->where('deleted', 0)
                    ->set([
                        'deleted' => 1,
                        'updated' => date('Y-m-d H:i:s')
                    ])
                    ->update();
    }

    /**
     * 🔹 Thêm hàng loạt phụ tùng mới (insertBatch)
     */
    public function bulkInsert($storeId, array $spareParts)
    {
        if (empty($spareParts)) return false;

        $timestamp = date('Y-m-d H:i:s');
        $data = array_map(function ($item) use ($storeId, $timestamp) {
            return [
                'FK_idStore'    => $storeId,
                'FK_idSparePart'=> $item['FK_idSparePart'],
                'stockQty'      => $item['stockQty'] ?? 0,
                'warningQty'    => $item['warningQty'] ?? 0,
                'location'      => $item['location'] ?? '',
                'deleted'       => 0,
                'created'       => $timestamp,
                'updated'       => $timestamp
            ];
        }, $spareParts);

        return $this->insertBatch($data);
    }
    
}
