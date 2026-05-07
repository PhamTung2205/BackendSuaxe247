<?php

namespace App\Controllers;

use App\Models\MSparePartCategory;
use CodeIgniter\RESTful\ResourceController;

class CSparePartCategory extends ResourceController
{
    protected $model; 

    public function __construct()
    {
        $this->model = new MSparePartCategory();
    }

    private function normalizeWhitespace(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $trimmedValue = trim($value);
                $data[$key] = preg_replace('/\s+/', ' ', $trimmedValue);
            }
        }
        return $data;
    }

    public function index()
    {
        $name  = $this->request->getVar('search');
        $page  = (int)($this->request->getVar('page') ?: 1);
        $limit = (int)($this->request->getVar('limit') ?: 10);
        $offset = ($page - 1) * $limit;

        $result = $this->model->getFilteredSparePartCategories($name, $limit, $offset);

        return $this->respond([
            'status'  => 'success',
            'data'    => $result['data'],
            'total'   => $result['total'],
            'page'    => $page,
            'perPage' => $limit,
        ]);
    }

    public function show($id = null)
    {
        $data = $this->model->findActive($id);
        if ($data) {
            return $this->respond(['status' => 'success', 'data' => $data]);
        }
        return $this->failNotFound('Không tìm thấy danh mục.');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $data = $this->normalizeWhitespace($data);
        // $data = array_map('trim', $data);

        if (empty($data['PK_idCategory'])) {
            return $this->fail(['error' => 'Mã danh mục không được để trống.'], 422);
        }
        if ($this->model->findActive($data['PK_idCategory'])) {
            return $this->fail(['error' => 'Mã danh mục đã tồn tại.'], 409); 
        }

        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Thêm danh mục thành công']);
        }

        return $this->fail($this->model->errors(), 422);
    }

    public function update($id = null)
    {
        if ($this->model->findActive($id) === null) {
            return $this->failNotFound('Không tìm thấy danh mục để cập nhật.');
        }
        $data = $this->request->getJSON(true);
        $data = $this->normalizeWhitespace($data);
        // $data = array_map('trim', $data);
        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Cập nhật thành công']);
        }
        
        return $this->fail($this->model->errors(), 422);
    }

    public function delete($id = null)
    {
        if ($this->model->findActive($id) === null) {
            return $this->failNotFound('Không tìm thấy danh mục để xóa.');
        }
        if ($this->model->softDelete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'Xóa danh mục thành công']);
        }
        
        return $this->fail('Xóa thất bại.', 500);
    }
}