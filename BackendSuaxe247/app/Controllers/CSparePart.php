<?php

namespace App\Controllers;

use App\Models\MSparePart;
use CodeIgniter\RESTful\ResourceController;

class CSparePart extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new MSparePart();
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
        $name = $this->request->getVar('search');
        $page  = (int)($this->request->getVar('page') ?: 1);
        $limit = (int)($this->request->getVar('limit') ?: 10);
        $offset = ($page - 1) * $limit;

        $result = $this->model->getFilteredSpareParts($name, $limit, $offset);

        return $this->respond([
            'status'  => 'success',
            'data'    => $result['data'],
            'total'   => $result['total'],
            'page'    => $page,
            'perPage' => $limit,
        ]);
    }

    public function getByStore($storeId = null)
    {
        if (empty($storeId)) {
            return $this->fail('Vui lòng cung cấp ID của cửa hàng.', 400);
        }

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('store_sparepart ssp');
            $builder->select('sp.PK_idSparePart, sp.sparePartName, sp.unit, sp.purchasePrice, sp.salePrice');
            $builder->join('sparepart sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left');
            $builder->where('ssp.FK_idStore', $storeId);
            $builder->where('ssp.deleted', 0);
            $builder->where('sp.deleted', 0);

            $data = $builder->get()->getResultArray();
            return $this->respond(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            log_message('error', '[CSparePart] getByStore: ' . $e->getMessage());
            return $this->failServerError('Không thể lấy danh sách phụ tùng của cửa hàng.');
        }
    }

    public function show($id = null)
    {
        $data = $this->model->findActive($id);
        if ($data) {
            return $this->respond(['status' => 'success', 'data' => $data]);
        }
        return $this->failNotFound('Không tìm thấy phụ tùng.');
    }

    public function create()
    {
        $data = $this->normalizeWhitespace($this->request->getJSON(true));

        if (isset($data['purchasePrice'], $data['salePrice']) &&
            (int)$data['salePrice'] < (int)$data['purchasePrice']) {
            return $this->failValidationErrors(['salePrice' => 'Giá bán không được nhỏ hơn giá mua.']);
        }

        try {
            log_message('debug', '[CSparePart] Creating SparePart: ' . json_encode($data));

            if ($this->model->insert($data)) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Thêm phụ tùng thành công'
                ]);
            }

            log_message('error', '[CSparePart] Validation errors: ' . json_encode($this->model->errors()));
            return $this->fail($this->model->errors(), 422);
        } catch (\Exception $e) {
            log_message('error', '[CSparePart] create(): ' . $e->getMessage());
            return $this->failServerError('Đã xảy ra lỗi không mong muốn.');
        }
    }

    public function update($id = null)
    {
        try {
            $existing = $this->model->findActive($id);
            if ($existing === null) {
                return $this->failNotFound('Không tìm thấy phụ tùng để cập nhật.');
            }

            $data = $this->normalizeWhitespace($this->request->getJSON(true));
            unset($data['PK_idSparePart']);

            $mergedData = array_merge($existing, $data);

            if (isset($mergedData['purchasePrice'], $mergedData['salePrice']) &&
                (int)$mergedData['salePrice'] < (int)$mergedData['purchasePrice']) {
                return $this->failValidationErrors(['salePrice' => 'Giá bán không được nhỏ hơn giá mua.']);
            }

            log_message('debug', '[CSparePart] Updating SparePart ' . $id . ': ' . json_encode($mergedData));

            if ($this->model->update($id, $mergedData)) {
                return $this->respond(['status' => 'success', 'message' => 'Cập nhật thành công']);
            }

            log_message('error', '[CSparePart] Validation errors: ' . json_encode($this->model->errors()));
            return $this->fail($this->model->errors(), 422);
        } catch (\Exception $e) {
            log_message('error', '[CSparePart] Exception in update(): ' . $e->getMessage());
            return $this->failServerError('Đã xảy ra lỗi không mong muốn.');
        }
    }

    public function delete($id = null)
    {
        if ($this->model->findActive($id) === null) {
            return $this->failNotFound('Không tìm thấy phụ tùng để xóa.');
        }

        if ($this->model->softDelete($id)) {
            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Xóa phụ tùng thành công'
            ]);
        }

        return $this->fail('Xóa thất bại.', 500);
    }
}
