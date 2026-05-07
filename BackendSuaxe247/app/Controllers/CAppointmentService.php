<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MAppointmentService;

/**
 * Controller: Quản lý bảng appointment_service
 * Cho phép: thêm, lấy, xóa, cập nhật các dịch vụ thuộc lịch hẹn.
 */
class CAppointmentService extends ResourceController
{
    use ResponseTrait;

    protected $modelName = MAppointmentService::class;
    protected $format    = 'json';

    /**
     * 🔹 Lấy toàn bộ bản ghi trong bảng appointment_service
     * GET /api/appointment-service/all
     */
    public function getAllAppointmentServices()
    {
        try {
            $data = $this->model->findAll();

            return $this->respond([
                'status' => 'success',
                'count'  => count($data),
                'data'   => $data
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi lấy dữ liệu: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Lấy danh sách dịch vụ theo ID lịch hẹn
     * GET /api/appointment-service/{appointmentId}
     */
    public function getServicesByAppointment($appointmentId = null)
    {
        try {
            if (!$appointmentId) {
                return $this->failValidationError('Thiếu ID lịch hẹn!');
            }

            $data = $this->model->getServicesByAppointment($appointmentId);

            return $this->respond([
                'status'  => 'success',
                'appointment_id' => $appointmentId,
                'count'   => count($data),
                'data'    => $data,
                'message' => count($data)
                    ? "Danh sách dịch vụ của lịch hẹn #{$appointmentId}"
                    : "Lịch hẹn #{$appointmentId} chưa có dịch vụ nào."
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi lấy danh sách dịch vụ: ' . $e->getMessage());
        }
    }
    /**
     * 🔹 Thêm các dịch vụ vào 1 lịch hẹn
     * POST /api/appointment-service/add
     * Body (FormData hoặc JSON):
     * {
     *   "FK_idAppointment": 5,
     *   "FK_idService": ["SVC01","SVC02"]
     * }
     */
    public function addServicesToAppointment()
    {
        try {
            // Nhận dữ liệu từ form-data hoặc JSON
            if (strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false) {
                $data = $this->request->getJSON(true);
            } else {
                $data = $this->request->getVar();
            }

            $appointmentId = $data['FK_idAppointment'] ?? null;
            $serviceIds    = $data['FK_idService'] ?? [];

            if (empty($appointmentId) || empty($serviceIds)) {
                return $this->failValidationError('Thiếu FK_idAppointment hoặc FK_idService!');
            }

            if (!is_array($serviceIds)) {
                $serviceIds = [$serviceIds];
            }

            $this->model->addServicesToAppointment((int)$appointmentId, $serviceIds);

            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'Thêm dịch vụ vào lịch hẹn thành công!',
                'FK_idAppointment' => $appointmentId,
                'services_added'   => $serviceIds
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi thêm dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa 1 dịch vụ cụ thể trong 1 lịch hẹn
     * DELETE /api/appointment-service/remove
     * Body:
     * {
     *   "FK_idAppointment": 5,
     *   "FK_idService": "SVC01"
     * }
     */
    public function removeServiceFromAppointment()
    {
        try {
            $data = $this->request->getJSON(true) ?? $this->request->getVar();
            $appointmentId = $data['FK_idAppointment'] ?? null;
            $serviceId     = $data['FK_idService'] ?? null;

            if (empty($appointmentId) || empty($serviceId)) {
                return $this->failValidationError('Thiếu FK_idAppointment hoặc FK_idService!');
            }

            $deleted = $this->model
                ->where('FK_idAppointment', (int)$appointmentId)
                ->where('FK_idService', $serviceId)
                ->delete();

            if ($deleted) {
                return $this->respondDeleted([
                    'status'  => 'success',
                    'message' => "Đã xóa dịch vụ {$serviceId} khỏi lịch hẹn #{$appointmentId}"
                ]);
            } else {
                return $this->failNotFound('Không tìm thấy bản ghi cần xóa.');
            }
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi xóa dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Xóa toàn bộ dịch vụ của 1 lịch hẹn
     * DELETE /api/appointment-service/remove-all/{appointmentId}
     */
    public function removeAllServicesFromAppointment($appointmentId = null)
    {
        try {
            if (!$appointmentId) {
                return $this->failValidationError('Thiếu FK_idAppointment!');
            }

            $this->model->removeAllServicesFromAppointment((int)$appointmentId);

            return $this->respondDeleted([
                'status'  => 'success',
                'message' => "Đã xóa toàn bộ dịch vụ trong lịch hẹn #{$appointmentId}"
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi xóa toàn bộ dịch vụ: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Cập nhật danh sách dịch vụ cho 1 lịch hẹn
     * PUT /api/appointment-service/update
     * Body:
     * {
     *   "FK_idAppointment": 5,
     *   "FK_idService": ["SVC02","SVC05"]
     * }
     */
    public function updateServicesForAppointment()
    {
        try {
            $data = $this->request->getJSON(true) ?? $this->request->getVar();
            $appointmentId = $data['FK_idAppointment'] ?? null;
            $serviceIds    = $data['FK_idService'] ?? [];

            if (empty($appointmentId)) {
                return $this->failValidationError('Thiếu FK_idAppointment!');
            }

            $this->model->updateServicesForAppointment((int)$appointmentId, $serviceIds);

            return $this->respond([
                'status' => 'success',
                'message' => "Đã cập nhật danh sách dịch vụ cho lịch hẹn #{$appointmentId}",
                'FK_idAppointment' => $appointmentId,
                'services' => $serviceIds
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Lỗi khi cập nhật dịch vụ: ' . $e->getMessage());
        }
    }
}
