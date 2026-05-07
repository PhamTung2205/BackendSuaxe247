<?php

namespace App\Models;

use CodeIgniter\Model;

class MSupplier extends Model
{
    protected $table = 'Supplier';
    protected $primaryKey = 'PK_idSupplier';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true; 
    protected $createdField = 'created';
    protected $updatedField = 'updated';

    protected $allowedFields = [
        'PK_idSupplier',
        'supplierName',
        'address',
        'email',
        'phone',
        'deleted' 
    ];

    protected $validationRules = [
        'supplierName'  => 'required|max_length[255]',
        'address'       => 'required',
        'email'         => 'permit_empty|valid_email',
        'phone'         => 'required|min_length[10]|max_length[15]|regex_match[/^0[3|5|7|8|9][0-9]{8}$/]',
    ];

    protected $validationMessages = [
        'supplierName' => ['required' => 'Tên nhà cung cấp không được để trống.'],
        'address' => ['required' => 'Địa chỉ không được để trống.'],
        'email' => ['valid_email' => 'Địa chỉ email không hợp lệ.'],
        'phone' => [
            'required' => 'Số điện thoại không được để trống.',
            'min_length' => 'Số điện thoại phải có ít nhất 10 chữ số.',
            'max_length' => 'Số điện thoại không được vượt quá 15 chữ số.',
            'regex_match' => 'Số điện thoại không đúng định dạng. Ví dụ hợp lệ: 0987654321'
        ],
    ];

    public function findActive($id)
    {
        return $this->where('deleted', 0)->find($id);
    }

    public function softDelete($id)
    {
        return $this->update($id, ['deleted' => 1]);
    }

    public function getFilteredSuppliers(?string $name = null, int $limit = 10, int $offset = 0)
    {
        $builder = $this->builder();
        $builder->where('deleted', 0);
        
        if (!empty($name)) {
            $builder->like('supplierName', $name);
        }

        $total = $builder->countAllResults(false);
        $builder->limit($limit, $offset);
        // $builder->orderBy('created', 'DESC');
        $data = $builder->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }
}