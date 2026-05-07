<?php

namespace App\Controllers;

use App\Models\MImportReceipt;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class CImportReceipt extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new MImportReceipt();
    }

    public function index()
    {
        try {
            $params = [
                'search' => $this->request->getGet('search'),
                'page'   => (int)($this->request->getGet('page') ?: 1),
                'limit'  => (int)($this->request->getGet('limit') ?: 10),
                'storeId' => $this->request->getGet('storeId'),
                'supplierId' => $this->request->getGet('supplierId'),
                'startDate' => $this->request->getGet('startDate'),
                'endDate' => $this->request->getGet('endDate')
            ];

            $result = $this->model->getFilteredImportReceipts($params);

            return $this->respond([
                'status'  => 'success',
                'data'    => $result['data'],
                'total'   => $result['total'],
                'page'    => $params['page'],
                'perPage' => $params['limit'],
            ]);
        } catch (Exception $e) {
            log_message('error', '[CImportReceipt] index: ' . $e->getMessage());
            return $this->failServerError('Không thể lấy danh sách phiếu nhập.');
        }
    }

    public function show($id = null)
    {
        try {
            $receipt = $this->model->getReceiptDetails($id);
            if (!$receipt) {
                return $this->failNotFound('Không tìm thấy phiếu nhập.');
            }
            return $this->respond(['status' => 'success', 'data' => $receipt]);
        } catch (Exception $e) {
            log_message('error', '[CImportReceipt] show: ' . $e->getMessage());
            return $this->failServerError('Không thể lấy chi tiết phiếu nhập.');
        }
    }

    public function create()
    {
        try {
            $data = $this->request->getJSON(true);

            if (empty($data['main']) || empty($data['details'])) {
                return $this->fail('Dữ liệu gửi lên không hợp lệ.', 400);
            }

            $mainData = $data['main'];
            $detailsData = $data['details'];
            
            $newReceiptId = $this->model->createImportReceipt($mainData, $detailsData);

            if ($newReceiptId) {
                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Tạo phiếu nhập kho thành công!',
                    'receiptId' => $newReceiptId
                ]);
            } else {
                return $this->fail('Tạo phiếu nhập kho thất bại không rõ nguyên nhân.', 500);
            }
        } catch (Exception $e) {
            log_message('error', '[CImportReceipt] create: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Không có ID phiếu nhập được cung cấp.', 400);
            }

            $receipt = $this->model->where('deleted', 0)->find($id);
            if (!$receipt) {
                return $this->failNotFound('Không tìm thấy phiếu nhập.');
            }

            $data = $this->request->getJSON(true);

            $updateData = [
                'FK_idSupplier' => $data['FK_idSupplier'] ?? null,
                'deliveryReceipt'  => $data['deliveryReceipt'] ?? null,
            ];

            $validation = \Config\Services::validation();
            $validation->setRules([
                'FK_idSupplier' => 'required',
                'deliveryReceipt'  => 'required',
            ]);

            if (!$validation->run($updateData)) {
                return $this->fail($validation->getErrors(), 400);
            }

            if ($this->model->update($id, $updateData)) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Cập nhật phiếu nhập kho thành công!'
                ]);
            } else {
                return $this->fail('Cập nhật phiếu nhập kho thất bại.', 500);
            }
        } catch (Exception $e) {
            log_message('error', '[CImportReceipt] update (' . $id . '): ' . $e->getMessage());
            return $this->failServerError('Không thể cập nhật phiếu nhập kho.');
        }
    }

    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('Không có ID phiếu nhập được cung cấp.', 400);
            }

            $receipt = $this->model->where('deleted', 0)->find($id);
            if (!$receipt) {
                return $this->failNotFound('Không tìm thấy phiếu nhập hoặc phiếu đã được xóa trước đó.');
            }

            if ($this->model->manualSoftDeleteWithDetails($id)) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Xóa phiếu nhập kho thành công!'
                ]);
            } else {
                return $this->fail('Xóa phiếu nhập kho thất bại do lỗi hệ thống.', 500);
            }
        } catch (Exception $e) {
            log_message('error', '[CImportReceipt] delete (' . $id . '): ' . $e->getMessage());
            return $this->failServerError('Không thể xóa phiếu nhập kho.');
        }
    }
}