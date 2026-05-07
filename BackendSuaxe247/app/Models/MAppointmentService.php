<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: Quản lý bảng appointment_service
 * Cột:
 *  - PK_id (int, tự tăng)
 *  - FK_idAppointment (int)
 *  - FK_idService (varchar)
 */
class MAppointmentService extends Model
{
    protected $table = 'appointment_service';
    protected $primaryKey = 'PK_id';
    protected $allowedFields = ['FK_idAppointment', 'FK_idService'];
    protected $useAutoIncrement = true;

    /**
     * 🔹 Lấy danh sách dịch vụ theo ID lịch hẹn
     */
   public function getServicesByAppointment($appointmentId)
    {
        return $this->db->table('appointment_service AS aps')
            ->select('aps.FK_idService, sv.serviceName')
            ->join('service AS sv', 'sv.PK_idService = aps.FK_idService', 'left')
            ->where('aps.FK_idAppointment', $appointmentId)
            ->get()
            ->getResultArray();
    }
    /**
     * 🔹 Thêm nhiều dịch vụ cho 1 lịch hẹn
     */
    public function addServicesToAppointment($appointmentId, $serviceIds)
    {
        if (empty($serviceIds)) return;

        if (!is_array($serviceIds)) {
            $serviceIds = [$serviceIds];
        }

        foreach ($serviceIds as $sid) {
            $data = [
                'FK_idAppointment' => (int)$appointmentId,
                'FK_idService'     => $sid
            ];
            $this->insert($data);
        }
    }

    /**
     * 🔹 Xóa toàn bộ dịch vụ của 1 lịch hẹn
     */
    public function removeAllServicesFromAppointment($appointmentId)
    {
        return $this->where('FK_idAppointment', (int)$appointmentId)->delete();
    }

    /**
     * 🔹 Cập nhật lại toàn bộ dịch vụ cho 1 lịch hẹn
     */
    public function updateServicesForAppointment($appointmentId, $serviceIds)
    {
        $this->removeAllServicesFromAppointment($appointmentId);
        $this->addServicesToAppointment($appointmentId, $serviceIds);
    }

    /**
     * 🔹 Lấy toàn bộ bản ghi
     */
    public function getAllAppointmentServices()
    {
        return $this->findAll();
    }
}
