<?php

namespace App\Controllers;

use App\Models\MService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;

class CService extends ResourceController
{
    protected $modelName = MService::class;
    protected $format    = 'json';

    /**
     * 🔹 Danh sách tất cả dịch vụ
     */
    public function index()
    {
        try {
            $services = $this->model->getAllService();

            return $this->respond([
                'status'  => 'success',
                'data'    => $services,
                'message' => count($services) ? 'Danh sách dịch vụ' : 'Không có dịch vụ nào'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy danh sách dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xem chi tiết dịch vụ
     */
    public function show($id = null)
    {
        try {
            $service = $this->model->getServiceById($id);

            if (!$service) {
                return $this->failNotFound('Không tìm thấy dịch vụ!');
            }

            return $this->respond([
                'status' => 'success',
                'data'   => $service,
                'message' => 'Thông tin dịch vụ'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi lấy dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Thêm dịch vụ mới
     */
    public function create()
    {
        helper(['form', 'url']);

        try {
            $data = $this->request->getPost();
            $file = $this->request->getFile('image');

            // Kiểm tra mã trùng
            if ($this->model->find($data['PK_idService'])) {
                return $this->failResourceExists('Mã dịch vụ đã tồn tại!');
            }

            $imageURL = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imageURL = $this->handleImageUpload($file, $data['PK_idService']);
            }

            $serviceData = [
                'PK_idService'   => trim($data['PK_idService']),
                'serviceName'    => trim($data['serviceName']),
                'description'    => trim($data['description']),
                'estimatedPrice' => trim($data['estimatedPrice']),
                'estimatedTime'  => trim($data['estimatedTime']),
                'imageURL'       => $imageURL,
                'deleted'        => 0
            ];

            if (!$this->model->insert($serviceData)) {
                throw new \Exception('Không thể thêm dịch vụ');
            }

            return $this->respondCreated([
                'status'  => 'success',
                'data'    => $serviceData,
                'message' => 'Thêm dịch vụ thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi thêm dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Cập nhật dịch vụ
     */
    public function update($id = null)
    {
        helper(['form', 'url']);

        try {
            $data = $this->request->getPost();
            $file = $this->request->getFile('image');

            $service = $this->model->find($id);
            if (!$service) {
                return $this->failNotFound('Không tìm thấy dịch vụ!');
            }

            $imageURL = $service['imageURL'];

            if ($file && $file->isValid() && !$file->hasMoved()) {
                if ($imageURL && file_exists(FCPATH . $imageURL)) {
                    unlink(FCPATH . $imageURL);
                }
                $imageURL = $this->handleImageUpload($file, $id);
            }

            $updateData = [
                'serviceName'    => trim($data['serviceName']),
                'description'    => trim($data['description']),
                'estimatedPrice' => trim($data['estimatedPrice']),
                'estimatedTime'  => trim($data['estimatedTime']),
                'imageURL'       => $imageURL,
                'updated'        => Time::now('Asia/Ho_Chi_Minh', 'en_US'),
            ];

            if (!$this->model->update($id, $updateData)) {
                throw new \Exception('Không thể cập nhật dịch vụ');
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $this->model->find($id),
                'message' => 'Cập nhật dịch vụ thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi cập nhật dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa mềm dịch vụ
     */
    public function delete($id = null)
    {
        try {
            $service = $this->model->find($id);
            if (!$service) {
                return $this->failNotFound('Không tìm thấy dịch vụ!');
            }

            if (!$this->model->deleteService($id)) {
                throw new \Exception('Không thể xóa dịch vụ');
            }

            return $this->respondDeleted([
                'status'  => 'success',
                'message' => 'Xóa dịch vụ thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi xóa dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Upload ảnh (Private)
     */
    private function handleImageUpload($file, $serviceId)
    {
        if (!$file->isValid()) {
            throw new \Exception('File upload không hợp lệ!');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Chỉ chấp nhận file ảnh (JPEG, PNG, JPG, GIF)!');
        }

        $ext = $file->getClientExtension();
        $fileName = $serviceId . '.' . $ext;
        $uploadPath = FCPATH . 'uploads/services/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $fileName, true);

        return '/uploads/services/' . $fileName;
    }
}
