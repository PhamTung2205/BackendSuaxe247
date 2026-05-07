<?php

namespace App\Controllers;

use App\Models\MSparePartInventory;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class CSparePartInventory extends ResourceController
{
    protected $modelName = MSparePartInventory::class;
    protected $format = 'json';

    /**
     * Xem danh sách tồn kho
     */
    public function index()
    {
        $userId = $this->request->getGet('user_id');
        if (empty($userId) || !is_numeric($userId)) {
            return $this->failValidationErrors('Vui lòng cung cấp ID người dùng hợp lệ.');
        }

        try {
            $limit = (int)($this->request->getGet('limit') ?? 10);
            $page = (int)($this->request->getGet('page') ?? 1);
            $offset = ($page - 1) * $limit;

            $result = $this->model->getFilteredSpareParts(
                (int)$userId,
                $this->request->getGet('search'),
                $this->request->getGet('category'),
                $this->request->getGet('store_id'),
                $limit,
                $offset
            );

            return $this->respond([
                'status'  => 'success',
                'data'    => $result['data'],
                'total'   => $result['total'],
                'page'    => $page,
                'perPage' => $limit,
                'message' => 'Lấy danh sách tồn kho thành công.'
            ]);
        } catch (Exception $e) {
            log_message('error', '[CSparePartInventory] index(): ' . $e->getMessage());
            return $this->failServerError('Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật chi tiết phụ tùng và tồn kho.
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $storeId = $data['FK_idStore'] ?? null;

        if (!$id || !$storeId) {
            return $this->failValidationErrors('Thiếu mã phụ tùng hoặc mã cửa hàng.');
        }

        try {
            if ($this->model->updateSparePartDetails($id, $storeId, $data)) {
                return $this->respond([
                    'status'  => 'success',
                    'message' => 'Cập nhật tồn kho thành công.'
                ]);
            }
            return $this->fail('Cập nhật thất bại không rõ nguyên nhân.', 400);
        } catch (Exception $e) {
            log_message('error', '[CSparePartInventory] update(): ' . $e->getMessage());
            return $this->failValidationErrors($e->getMessage());
        }
    }
}
