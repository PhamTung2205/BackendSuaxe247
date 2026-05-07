<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MAppointment;
use CodeIgniter\I18n\Time;

class CAppointment extends ResourceController
{
    protected $modelName = MAppointment::class;
    protected $format    = 'json';

    /**
     * 🔹 Lấy tất cả lịch hẹn
     */
    public function index()
{
    try {
        $appointments = $this->model->getAllAppointment();

        return $this->respond([
            'status'  => 'success',
            'message' => count($appointments) ? 'Danh sách tất cả cuộc hẹn' : 'Không có cuộc hẹn nào',
            'data'    => $appointments,
            'count'   => count($appointments),
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi lấy danh sách cuộc hẹn: ' . $e->getMessage());
    }
}
    /**
     * 🔹 Lấy lịch hẹn theo khách hàng
     */
   public function getByCustomer($customerId = null)
{
    if (!$customerId) {
        return $this->failValidationError('Thiếu ID khách hàng');
    }

    try {
        $appointments = $this->model->getAppointmentsByCustomer($customerId);

        return $this->respond([
            'status'  => 'success',
            'message' => count($appointments)
                ? "Danh sách cuộc hẹn của khách hàng {$customerId}"
                : "Khách hàng {$customerId} chưa có cuộc hẹn nào",
            'data'    => $appointments,
            'count'   => count($appointments),
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi lấy cuộc hẹn của khách hàng: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Lấy lịch hẹn theo cửa hàng
     */
    public function getByStore($storeId = null)
{
    if (!$storeId) {
        return $this->failValidationError('Thiếu ID cửa hàng');
    }

    try {
        $appointments = $this->model->getAppointmentsByStore($storeId);

        return $this->respond([
            'status'  => 'success',
            'message' => count($appointments)
                ? "Danh sách cuộc hẹn của cửa hàng {$storeId}"
                : "Cửa hàng {$storeId} chưa có cuộc hẹn nào",
            'data'    => $appointments,
            'count'   => count($appointments),
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi lấy cuộc hẹn của cửa hàng: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Xem chi tiết 1 cuộc hẹn
     */
    public function show($id = null)
{
    try {
        $appointment = $this->model->getAppointmentById($id);

        if (!$appointment) {
            return $this->failNotFound('Không tìm thấy cuộc hẹn!');
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Thông tin cuộc hẹn',
            'data'    => $appointment,
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi lấy cuộc hẹn: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Thêm cuộc hẹn mới
     */
   /**
 * 🔹 Thêm cuộc hẹn mới (phiên bản đã sửa)
 */
public function create()
{
    helper(['form', 'url']);

    try {
        // ✅ Lấy dữ liệu từ JSON hoặc form-data
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // ✅ Các trường bắt buộc (bỏ PK_idAppointment)
        $required = ['FK_idStore', 'FK_idCustomer', 'FK_idVehicle', 'appointmentTime', 'appointmentDate'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("Trường {$field} là bắt buộc!");
            }
        }

        // ❌ Không cho phép client gửi PK_idAppointment (vì auto_increment)
        unset($data['PK_idAppointment']);

        // ✅ Chuẩn bị dữ liệu thêm
        $appointment = [
            'FK_idStore'       => trim($data['FK_idStore']),
            'FK_idCustomer'    => trim($data['FK_idCustomer']),
            'FK_idVehicle'     => trim($data['FK_idVehicle']),
            'appointmentTime'  => trim($data['appointmentTime']),
            'appointmentDate'  => trim($data['appointmentDate']),
            'status'           => $data['status'] ?? 'Chờ xác nhận',
            'created'          => Time::now('Asia/Ho_Chi_Minh', 'en_US'),
            'updated'          => null,
            'deleted'          => 0,
        ];

        // ✅ Thực hiện insert
        if (!$this->model->insert($appointment)) {
            return $this->failServerError('Không thể thêm cuộc hẹn.');
        }

        // ✅ Lấy ID thật vừa tạo
        $insertId = $this->model->getInsertID();

        // ✅ Trả về JSON kết quả
        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Thêm cuộc hẹn thành công!',
            'data'    => [
                'PK_idAppointment' => $insertId,
                'FK_idStore'       => $appointment['FK_idStore'],
                'FK_idCustomer'    => $appointment['FK_idCustomer'],
                'FK_idVehicle'     => $appointment['FK_idVehicle'],
                'appointmentTime'  => $appointment['appointmentTime'],
                'appointmentDate'  => $appointment['appointmentDate'],
                'status'           => $appointment['status'],
            ],
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi thêm cuộc hẹn: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Cập nhật cuộc hẹn
     */
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true) ?? $this->request->getPost();

            $appointment = $this->model->find($id);
            if (!$appointment) {
                return $this->failNotFound('Không tìm thấy cuộc hẹn!');
            }

            $updateData = ['updated' => Time::now('Asia/Ho_Chi_Minh', 'en_US')];
            $allowed = ['FK_idStore', 'FK_idCustomer', 'FK_idVehicle', 'appointmentTime', 'appointmentDate', 'status'];

            foreach ($allowed as $f) {
                if (isset($data[$f])) $updateData[$f] = $data[$f];
            }

            if (!$this->model->update($id, $updateData)) {
                throw new \Exception('Không thể cập nhật cuộc hẹn.');
            }

            return $this->respond([
                'status'  => 'success',
                'message' => 'Cập nhật cuộc hẹn thành công!',
                'data'    => $this->model->find($id),
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi cập nhật cuộc hẹn: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa mềm cuộc hẹn
     */
    public function delete($id = null)
    {
        try {
            $appointment = $this->model->find($id);
            if (!$appointment) {
                return $this->failNotFound('Không tìm thấy cuộc hẹn!');
            }

            if (!$this->model->update($id, [
                'deleted' => 1,
                'updated' => Time::now('Asia/Ho_Chi_Minh', 'en_US'),
            ])) {
                throw new \Exception('Không thể xóa cuộc hẹn.');
            }

            return $this->respondDeleted([
                'status'  => 'success',
                'message' => 'Xóa cuộc hẹn thành công!',
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi xóa cuộc hẹn: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Tìm kiếm cuộc hẹn
     */
    public function search()
{
    try {
        $params = $this->request->getGet();
        
        // Thêm các filter mới
        $filters = [
            'FK_idStore' => $params['FK_idStore'] ?? null,
            'FK_idCustomer' => $params['FK_idCustomer'] ?? null,
            'FK_idVehicle' => $params['FK_idVehicle'] ?? null,
            'status' => $params['status'] ?? null,
            'appointmentDate' => $params['appointmentDate'] ?? null,
            'appointmentDate_from' => $params['appointmentDate_from'] ?? null,
            'appointmentDate_to' => $params['appointmentDate_to'] ?? null,
            'customer_name' => $params['customer_name'] ?? null,
            'license_plate' => $params['license_plate'] ?? null,
        ];

        $appointments = $this->model->searchAppointments($filters);

        return $this->respond([
            'status'  => 'success',
            'message' => count($appointments)
                ? 'Kết quả tìm kiếm cuộc hẹn'
                : 'Không tìm thấy cuộc hẹn nào phù hợp',
            'data'    => $appointments,
            'count'   => count($appointments),
            'filters' => $filters,
        ]);
    } catch (\Exception $e) {
        return $this->failServerError('Lỗi khi tìm kiếm cuộc hẹn: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Cập nhật trạng thái cuộc hẹn
     */
    public function updateStatus($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            if (empty($data['status'])) {
                return $this->failValidationError('Trường status là bắt buộc!');
            }

            $appointment = $this->model->find($id);
            if (!$appointment) {
                return $this->failNotFound('Không tìm thấy cuộc hẹn!');
            }

            if (!$this->model->update($id, [
                'status'  => $data['status'],
                'updated' => Time::now('Asia/Ho_Chi_Minh', 'en_US'),
            ])) {
                throw new \Exception('Không thể cập nhật trạng thái cuộc hẹn.');
            }

            return $this->respond([
                'status'  => 'success',
                'message' => 'Cập nhật trạng thái cuộc hẹn thành công!',
                'data'    => $this->model->find($id),
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi cập nhật trạng thái cuộc hẹn: ' . $e->getMessage());
        }
    }

    public function confirmedAppointmentsByStore($storeId = null)
    {
        if (!$storeId) {
            return $this->respond([
                'status'  => 'error',
                'data'    => [],
                'message' => 'Thiếu ID cửa hàng'
            ], 400);
        }

        try {
            $appointmentModel = new MAppointment();
            $appointments = $appointmentModel->getConfirmedAppointmentsByStore($storeId);

            if (!$appointments || count($appointments) === 0) {
                return $this->respond([
                    'status'  => 'success',
                    'data'    => [],
                    'message' => 'Không có lịch hẹn đã xác nhận nào cho cửa hàng này'
                ]);
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $appointments,
                'message' => 'Danh sách lịch hẹn đã xác nhận của cửa hàng'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Lỗi khi lấy danh sách lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }


      public function countByStoreAndTime($storeId = null)
    {
        if (!$storeId) {
            return $this->failValidationError('Thiếu ID cửa hàng');
        }

        $date = $this->request->getGet('date');
        $time = $this->request->getGet('time');

        try {
            $model = new MAppointment();
            $result = $model->countAppointmentsByStoreAndTime($storeId, $date, $time);

            return $this->respond([
                'status'  => 'success',
                'message' => "Thống kê lịch hẹn của cửa hàng {$storeId}" 
                            . ($date ? " vào ngày {$date}" : "")
                            . ($time ? " lúc {$time}" : ""),
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Lỗi khi thống kê lịch hẹn: ' . $e->getMessage());
        }
    }
    
}
