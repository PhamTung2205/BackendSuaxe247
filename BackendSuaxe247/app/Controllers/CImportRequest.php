<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time; 
use App\Models\MImportRequest;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class CImportRequest extends ResourceController
{
    protected $importRequestModel;
    
    public function __construct()
    {
        $this->importRequestModel = new MImportRequest();
    }

    /**
     * Lấy danh sách yêu cầu nhập hàng (có lọc, phân trang, phân quyền).
     */
    public function index()
    {
        $storeId = $this->request->getGet('store_id');
        $status = $this->request->getGet('status');
        $searchTerm = $this->request->getGet('search');

        $page  = (int)($this->request->getGet('page') ?: 1);
        $limit = (int)($this->request->getGet('limit') ?: 10);
        $offset = ($page - 1) * $limit;
        if ($offset < 0) $offset = 0;

        try {
            $result = $this->importRequestModel->getFilteredImportRequests($searchTerm, $storeId, $status, $limit, $offset);

            return $this->respond([
                'status'  => 'success',
                'data'    => $result['data'] ?? [],
                'total'   => $result['total'] ?? 0,
                'page'    => $page,
                'perPage' => $limit,
                'message' => 'Danh sách yêu cầu nhập'
            ]);
        } catch (Exception $e) {
            return $this->fail('Lỗi tải danh sách yêu cầu nhập hàng. Lỗi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lấy chi tiết một yêu cầu nhập hàng.
     */
    public function show($id = null)
    {
        try {
            $importRequest = $this->importRequestModel->getImportRequestById($id);
            
            if ($importRequest) {
                return $this->respond([
                    'status' => 'success',
                    'data' => $importRequest
                ]);
            }
        } catch (Exception $e) {
            return $this->fail('Lỗi khi lấy chi tiết yêu cầu nhập hàng.', 500);
        }

        return $this->failNotFound('Không tìm thấy yêu cầu nhập hàng.');
    }

    /**
     * Tạo yêu cầu nhập hàng mới.
     */
    public function create()
    {
        $data = $this->request->getJSON(true);
        $data['created'] = date('Y-m-d H:i:s');
        
        // Lấy ID người tạo từ JSON body hoặc Query Parameter 'user_id'
        $creatorId = $data['FK_idCreatedBy'] ?? $this->request->getGet('user_id');

        // Thiết lập giá trị
        $data['status'] = $data['status'] ?? 'Pending';
        $data['PK_idRequest'] = $data['PK_idRequest'] ?? 'IR' . substr(time(), -10); 
        $data['FK_idCreatedBy'] = $creatorId; 

        // Kiểm tra các trường bắt buộc
        if (empty($data['FK_idStore']) || empty($data['FK_idCreatedBy'])) {
             return $this->fail('Thiếu thông tin Kho (`FK_idStore`) hoặc Người tạo (`FK_idCreatedBy` / `user_id`).', 400);
        }

        try {
            if ($this->importRequestModel->insert($data)) {
                $newRequest = $this->importRequestModel->getImportRequestById($data['PK_idRequest']);
                
                return $this->respondCreated([
                    'status'  => 'success',
                    'message' => 'Tạo yêu cầu nhập hàng thành công',
                    'data'    => $newRequest
                ]);
            }
        } catch (Exception $e) {
            return $this->fail('Tạo yêu cầu nhập hàng thất bại. Lỗi: ' . $e->getMessage(), 500);
        }
        
        return $this->failValidationErrors();
    }

    /**
     * Cập nhật thông tin yêu cầu nhập hàng.
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        
        // Bảng importrequest không có cột updated, Model sẽ tự loại bỏ

        try {
            if ($this->importRequestModel->updateImportRequest($id, $data)) {
                
                $updatedRequest = $this->importRequestModel->find($id); 
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Cập nhật yêu cầu nhập hàng thành công',
                    'data' => $updatedRequest 
                ]);
            }
        } catch (Exception $e) {
            return $this->fail('Cập nhật yêu cầu nhập hàng thất bại. Lỗi: ' . $e->getMessage(), 500);
        }
        
        return $this->failNotFound('Không tìm thấy yêu cầu nhập hàng.');
    }

    /**
     * Xóa một yêu cầu nhập hàng (soft delete).
     */
    public function delete($id = null)
    {
        try {
            if ($this->importRequestModel->deleteImportRequest($id)) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Đã xóa yêu cầu nhập hàng thành công',
                ]);
            }
        } catch (Exception $e) {
            return $this->fail('Xóa yêu cầu nhập hàng thất bại. Lỗi: ' . $e->getMessage(), 500);
        }
        
        return $this->failNotFound('Không tìm thấy yêu cầu nhập hàng.');
    }
}