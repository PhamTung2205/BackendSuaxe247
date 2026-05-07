<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MStoreSparePart;
use CodeIgniter\I18n\Time;

class CStoreSparePart extends ResourceController
{
    use ResponseTrait;

    protected $modelName = MStoreSparePart::class;
    protected $format    = 'json';

    /**
     * 🔹 Lấy toàn bộ danh sách phụ tùng trong tất cả cửa hàng
     */
    public function index()
    {
        try {
            $data = $this->model->getAllStoreSpareParts();

            return $this->respond([
                'status'  => 'success',
                'count'   => count($data),
                'data'    => $data,
                'message' => count($data) ? 'Danh sách phụ tùng trong các cửa hàng' : 'Chưa có phụ tùng nào'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy danh sách: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Lấy danh sách phụ tùng theo cửa hàng
     */
    public function getByStore($storeId = null)
    {
        try {
            if (!$storeId) {
                return $this->failValidationError('Thiếu ID cửa hàng!');
            }

            $data = $this->model->getByStore($storeId);

            return $this->respond([
                'status'  => 'success',
                'storeId' => $storeId,
                'count'   => count($data),
                'data'    => $data,
                'message' => count($data)
                    ? "Danh sách phụ tùng của cửa hàng {$storeId}"
                    : "Cửa hàng {$storeId} chưa có phụ tùng nào"
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy danh sách phụ tùng của cửa hàng: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Thêm phụ tùng mới vào kho
     */
    public function create()
    {
        helper(['form', 'url']);

        try {
            $data = $this->request->getJSON(true);
            if (!$data) {
                $data = $this->request->getPost();
            }

            $required = ['PK_idSSP', 'FK_idStore', 'FK_idSparePart', 'stockQty', 'warningQty'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->failValidationError("Trường {$field} là bắt buộc!");
                }
            }

            $this->model->addStoreSparePart($data);

            return $this->respondCreated([
                'status'  => 'success',
                'data'    => $data,
                'message' => 'Thêm phụ tùng vào kho thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi thêm phụ tùng: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Cập nhật thông tin phụ tùng
     */
    public function update($id = null)
    {
        helper(['form', 'url']);

        try {
            if (!$id) {
                return $this->failValidationError('Thiếu ID phụ tùng trong kho!');
            }

            $data = $this->request->getJSON(true);
            if (!$data) {
                $data = $this->request->getRawInput();
            }

            $old = $this->model->find($id);
            if (!$old || $old['deleted'] == 1) {
                return $this->failNotFound('Không tìm thấy phụ tùng trong kho!');
            }

            $data['updated'] = Time::now('Asia/Ho_Chi_Minh', 'en_US');
            $this->model->update($id, $data);

            return $this->respond([
                'status'  => 'success',
                'data'    => $this->model->find($id),
                'message' => 'Cập nhật phụ tùng thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa phụ tùng (soft delete)
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('Thiếu ID phụ tùng trong kho!');
            }

            $record = $this->model->find($id);
            if (!$record || $record['deleted'] == 1) {
                return $this->failNotFound('Không tìm thấy phụ tùng trong kho!');
            }

            $this->model->softDelete($id);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Xóa phụ tùng khỏi kho thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Lấy danh sách phụ tùng sắp hết hàng
     */
    public function getLowStock($storeId = null)
    {
        try {
            $data = $this->model->getLowStockItems($storeId);

            return $this->respond([
                'status'  => 'success',
                'storeId' => $storeId,
                'count'   => count($data),
                'data'    => $data,
                'message' => count($data)
                    ? 'Danh sách phụ tùng sắp hết hàng'
                    : 'Không có phụ tùng nào sắp hết hàng'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy danh sách cảnh báo: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Lấy thông tin chi tiết 1 phụ tùng trong kho
     */
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationError('Thiếu ID phụ tùng!');
            }

            $data = $this->model->find($id);
            if (!$data || $data['deleted'] == 1) {
                return $this->failNotFound('Không tìm thấy phụ tùng!');
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $data,
                'message' => 'Thông tin chi tiết phụ tùng'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy thông tin phụ tùng: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Thêm hàng loạt phụ tùng cho cửa hàng mới (bulk insert)
     */
    public function sparePartsInsert()
    {
        try {
            $data = $this->request->getJSON(true);
            $storeId = $data['storeId'] ?? null;
            $spareParts = $data['spareParts'] ?? [];

            if (!$storeId || empty($spareParts)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Thiếu dữ liệu (storeId hoặc danh sách phụ tùng)!'
                ], 400);
            }

            // Gán FK_idStore cho từng phần tử nếu frontend chưa làm
            foreach ($spareParts as &$item) {
                $item['FK_idStore'] = $storeId;
                $item['deleted'] = 0;
            }

            // Thực hiện insert batch
            $this->model->insertBatch($spareParts);

            return $this->respond([
                'success' => true,
                'message' => 'Đã thêm danh sách phụ tùng vào kho'
            ], 200);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi bulk insert: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa mềm toàn bộ phụ tùng theo cửa hàng
     */
    public function softDeleteByStore($storeId = null)
    {
        try {
            if (!$storeId) {
                return $this->failValidationError('Thiếu ID cửa hàng!');
            }

            $model = new MStoreSparePart();
            $updated = $model->softDeleteByStore($storeId);

            if ($updated) {
                return $this->respond([
                    'success' => true,
                    'message' => "Đã xóa mềm toàn bộ phụ tùng của cửa hàng {$storeId}"
                ]);
            }

            return $this->respond([
                'success' => false,
                'message' => 'Không có phụ tùng nào để xóa hoặc lỗi khi cập nhật.'
            ], 400);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi xóa mềm phụ tùng theo cửa hàng: ' . $e->getMessage());
        }
    }
}
