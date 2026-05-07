<?php

namespace App\Models;

use CodeIgniter\Model;

class MSparePart extends Model
{
    protected $table            = 'sparepart';
    protected $primaryKey       = 'PK_idSparePart';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created';
    protected $updatedField     = 'updated';

    protected $allowedFields = [
        'PK_idSparePart', 'sparePartName', 'unit',
        'purchasePrice', 'salePrice', 'description', 'FK_idCategory', 'deleted'
    ];

    protected $afterInsert = ['allocateToStores'];
    protected $beforeInsert = ['manualValidation'];
    protected $beforeUpdate = ['manualValidation'];

    protected $validationRules = [
        'PK_idSparePart' => 'required|alpha_numeric_punct|min_length[3]|max_length[20]',
        'FK_idCategory'  => 'required',
        'sparePartName'  => 'required|min_length[2]|max_length[255]',
        'unit'           => 'required|min_length[1]|max_length[50]',
        'purchasePrice'  => 'required|is_natural_no_zero',
        'salePrice'      => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'PK_idSparePart' => [
            'required' => 'Mã phụ tùng không được để trống.',
        ],
        'sparePartName' => [
            'required' => 'Tên phụ tùng không được để trống.',
        ],
        'unit' => [
            'required' => 'Đơn vị tính không được để trống.',
        ],
        'purchasePrice' => [
            'required' => 'Giá mua không được để trống.',
            'is_natural_no_zero' => 'Giá mua phải là số nguyên dương lớn hơn 0.',
        ],
        'salePrice' => [
            'required' => 'Giá bán không được để trống.',
            'is_natural_no_zero' => 'Giá bán phải là số nguyên dương lớn hơn 0.',
        ],
        'FK_idCategory' => [
            'required' => 'Vui lòng chọn danh mục.',
        ],
    ];

    protected function manualValidation(array $data)
    {
        if (!isset($data['data'])) return $data;
        $d = &$data['data'];

        if (!preg_match('/^[0-9]+$/', (string)($d['purchasePrice'] ?? ''))) {
            $this->errors['purchasePrice'] = 'Giá mua phải là số nguyên hợp lệ (không chứa ký tự đặc biệt).';
        }

        if (!preg_match('/^[0-9]+$/', (string)($d['salePrice'] ?? ''))) {
            $this->errors['salePrice'] = 'Giá bán phải là số nguyên hợp lệ (không chứa ký tự đặc biệt).';
        }

        if (
            isset($d['purchasePrice'], $d['salePrice'])
            && (int)$d['salePrice'] < (int)$d['purchasePrice']
        ) {
            $this->errors['salePrice'] = 'Giá bán không được nhỏ hơn giá mua.';
        }

        if (!empty($this->errors)) {
            $data['result'] = false;
        }

        return $data;
    }

    protected function allocateToStores(array $data)
    {
        if (!$data['result']) return $data;

        $newSparePartId = $data['data']['PK_idSparePart'] ?? $data['id'] ?? null;
        if ($newSparePartId === null) return $data;

        $stores = $this->db->table('store')->where('deleted', 0)->get()->getResultArray();
        if (empty($stores)) return $data;

        $batch = [];
        foreach ($stores as $store) {
            $batch[] = [
                'FK_idStore'     => $store['PK_idStore'],
                'FK_idSparePart' => $newSparePartId,
                'stockQty'       => 0,
                'warningQty'     => 10,
                'location'       => 'Chưa xác định',
                'created'        => date('Y-m-d H:i:s'),
                'updated'        => date('Y-m-d H:i:s'),
            ];
        }

        try {
            $this->db->table('store_sparepart')->insertBatch($batch);
        } catch (\Exception $e) {
            log_message('error', '[MSparePart] allocateToStores error: ' . $e->getMessage());
        }

        return $data;
    }

    public function findActive($id)
    {
        return $this->where('deleted', 0)->find($id);
    }

    public function softDelete($id)
    {
        $this->db->transStart();
        $this->update($id, ['deleted' => 1, 'updated' => date('Y-m-d H:i:s')]);
        $this->db->table('store_sparepart')
            ->where('FK_idSparePart', $id)
            ->update(['deleted' => 1, 'updated' => date('Y-m-d H:i:s')]);
        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function getFilteredSpareParts(?string $name = null, int $limit = 10, int $offset = 0)
    {
        $builder = $this->builder();
        $builder->where('sparepart.deleted', 0);
        $builder->select('sparepart.*, sparepartcategory.categoryName');
        $builder->join('sparepartcategory', 'sparepartcategory.PK_idCategory = sparepart.FK_idCategory', 'left');

        if (!empty($name)) $builder->like('sparepart.sparePartName', $name);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $builder->limit($limit, $offset);
        $builder->orderBy('sparepart.created', 'DESC');
        $data = $builder->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }
}
