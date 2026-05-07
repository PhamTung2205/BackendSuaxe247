<?php

namespace App\Controllers;

use App\Models\MStore;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;

class CStore extends ResourceController
{
    protected $modelName = MStore::class;
    protected $format    = 'json';

    /**
     * 🔹 Danh sách cửa hàng (chưa bị xóa)
     */
    public function index()
    {
        try {
            $stores = $this->model->getAllStore();

            return $this->respond([
                'status'  => 'success',
                'data'    => $stores,
                'message' => count($stores) ? 'Danh sách cửa hàng' : 'Không có cửa hàng nào'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }
     public function index2()
    {
        try {
            $stores = $this->model->getAllStoreExceptKho();

            return $this->respond([
                'status'  => 'success',
                'data'    => $stores,
                'message' => count($stores) ? 'Danh sách cửa hàng' : 'Không có cửa hàng nào'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Xem chi tiết cửa hàng
     */
    public function show($id = null)
    {
        try {
            $store = $this->model->getStoreById($id);

            if (!$store) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy cửa hàng!'
                ], 404);
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $store,
                'message' => 'Thông tin cửa hàng'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Thêm cửa hàng mới
     */
    public function create()
    {
        helper(['form', 'url']);

        try {
            $data = $this->request->getPost();
            $file = $this->request->getFile('image');

            // Kiểm tra mã trùng
            if ($this->model->find($data['PK_idStore'])) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Mã cửa hàng đã tồn tại!'
                ], 409);
            }

            $imageURL = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imageURL = $this->handleImageUpload($file, $data['PK_idStore']);
            }

            $storeData = [
                'PK_idStore' => trim($data['PK_idStore']),
                'address'    => trim($data['address']),
                'phone'      => trim($data['phone']),
                'imageURL'   => $imageURL,
                'deleted'    => 0,
            ];

            if (!$this->model->insert($storeData)) {
                throw new \Exception('Không thể thêm cửa hàng');
            }

            return $this->respondCreated([
                'status'  => 'success',
                'data'    => $storeData,
                'message' => 'Thêm cửa hàng thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi thêm cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Cập nhật cửa hàng
     */
    public function update($id = null)
    {
        helper(['form', 'url']);

        try {
            $data = $this->request->getPost();
            $file = $this->request->getFile('image');

            $store = $this->model->find($id);
            if (!$store) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy cửa hàng!'
                ], 404);
            }

            $imageURL = $store['imageURL'];

            if ($file && $file->isValid() && !$file->hasMoved()) {
                if ($imageURL && file_exists(FCPATH . $imageURL)) {
                    unlink(FCPATH . $imageURL);
                }
                $imageURL = $this->handleImageUpload($file, $id);
            }

            $updateData = [
                'address'  => trim($data['address']),
                'phone'    => trim($data['phone']),
                'imageURL' => $imageURL,
                'updated'  => Time::now('Asia/Ho_Chi_Minh', 'en_US'),
            ];

            if (!$this->model->update($id, $updateData)) {
                throw new \Exception('Không thể cập nhật cửa hàng');
            }

            $updatedStore = $this->model->find($id);

            return $this->respond([
                'status'  => 'success',
                'data'    => $updatedStore,
                'message' => 'Cập nhật cửa hàng thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi cập nhật cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Xóa mềm cửa hàng
     */
    public function delete($id = null)
    {
        try {
            $store = $this->model->find($id);
            if (!$store) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy cửa hàng!'
                ], 404);
            }

            if (!$this->model->deleteStore($id)) {
                throw new \Exception('Không thể xóa cửa hàng');
            }

            return $this->respond([
                'status'  => 'success',
                'message' => 'Xóa cửa hàng thành công!'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi xóa cửa hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Upload ảnh (private)
     */
    private function handleImageUpload($file, $storeId)
    {
        if (!$file->isValid()) {
            throw new \Exception('File upload không hợp lệ!');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Chỉ chấp nhận file ảnh (JPEG, PNG, JPG, GIF)!');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('Kích thước ảnh tối đa 5MB!');
        }

        $ext = $file->getClientExtension();
        $fileName = $storeId . '.' . $ext;
        $uploadPath = FCPATH . 'uploads/stores/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $file->move($uploadPath, $fileName, true);

        return '/uploads/stores/' . $fileName;
    }
}
