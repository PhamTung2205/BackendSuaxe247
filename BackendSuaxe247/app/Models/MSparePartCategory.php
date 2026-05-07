<?php

namespace App\Models;

use CodeIgniter\Model;

class MSparePartCategory extends Model
{
    protected $table = 'SparePartCategory'; 
    protected $primaryKey = 'PK_idCategory';
    protected $useAutoIncrement = false; 
    protected $returnType = 'array';

    protected $useSoftDeletes = false;

    protected $useTimestamps = true; 
    protected $createdField = 'created';
    protected $updatedField = 'updated';

    protected $allowedFields = [
        'PK_idCategory',
        'categoryName',
        'description',
        'deleted' 
    ]; 
    
    protected $validationRules = [
        // 'PK_idCategory' => 'required|alpha_numeric|max_length[20]|is_unique[SparePartCategory.PK_idCategory,PK_idCategory,{PK_idCategory}]',
        'PK_idCategory' => 'permit_empty', 
        'categoryName'  => 'required|trim|max_length[255]',
        'description'   => 'permit_empty|trim|max_length[1000]'
    ];

    protected $validationMessages = [
        'categoryName' => [
            'required'   => 'Tên danh mục không được để trống.',
            'max_length' => 'Tên danh mục quá dài.',
            'is_unique'  => 'Tên danh mục này đã tồn tại.'
        ],
        'description' => [
            'max_length' => 'Mô tả quá dài.'
        ]
    ];

    public function findActive($id)
    {
        return $this->where('deleted', 0)->find($id);
    }

    public function softDelete($id)
    {
        return $this->update($id, ['deleted' => 1]);
    }

    public function getFilteredSparePartCategories($name = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();
        $builder->where('deleted', 0);

        if (!empty($name)) {
            $builder->like('categoryName', $name);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $builder->limit($limit, $offset);
        // $builder->orderBy('created', 'DESC'); 
        $data = $builder->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }
}