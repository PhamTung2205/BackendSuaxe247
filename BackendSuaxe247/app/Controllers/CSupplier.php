<?php

namespace App\Controllers;

use App\Models\MSupplier;
use CodeIgniter\RESTful\ResourceController;

class CSupplier extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new MSupplier();
    }

    public function index()
    {
        $name = $this->request->getVar('search');
        $page  = (int)($this->request->getVar('page') ?: 1);
        $limit = (int)($this->request->getVar('limit') ?: 10);
        $offset = ($page - 1) * $limit;
        $result = $this->model->getFilteredSuppliers($name, $limit, $offset);

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
        return $this->failNotFound('Không tìm thấy nhà cung cấp.');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['PK_idSupplier'])) {
            return $this->fail('Mã nhà cung cấp không được để trống.', 422);
        }
        if ($this->model->findActive($data['PK_idSupplier'])) {
            return $this->fail('Mã nhà cung cấp đã tồn tại.', 409); 
        }
        
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Thêm nhà cung cấp thành công']);
        }

        return $this->fail($this->model->errors(), 422);
    }

    public function update($id = null)
    {
        if ($this->model->findActive($id) === null) {
            return $this->failNotFound('Không tìm thấy nhà cung cấp để cập nhật.');
        }

        $data = $this->request->getJSON(true);

        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Cập nhật thành công']);
        }
        
        return $this->fail($this->model->errors(), 422);
    }

    public function delete($id = null)
    {
        if ($this->model->findActive($id) === null) {
            return $this->failNotFound('Không tìm thấy nhà cung cấp để xóa.');
        }

        if ($this->model->softDelete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'Xóa nhà cung cấp thành công']);
        }
        
        return $this->fail('Xóa thất bại.', 500);
    }
}