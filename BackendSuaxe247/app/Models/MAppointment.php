<?php

namespace App\Models;

use CodeIgniter\Model;

class MAppointment extends Model
{
    protected $table = 'Appointment';
    protected $primaryKey = 'PK_idAppointment';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'PK_idAppointment',
        'FK_idStore',
        'FK_idCustomer',
        'FK_idVehicle',
        'appointmentTime',
        'appointmentDate',
        'status',
        'created',
        'updated',
        'deleted'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';
    protected $deletedField  = 'deleted';

    /**
     * 🔹 Lấy tất cả appointment với thông tin đầy đủ
     */
    public function getAllAppointment()
    {
        return $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.phone as customer_phone,
            Customer.email as customer_email,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0)
        ->orderBy('Appointment.appointmentDate', 'DESC')
        ->orderBy('Appointment.appointmentTime', 'DESC')
        ->findAll();
    }

    /**
     * 🔹 Lấy appointment theo ID với thông tin đầy đủ
     */
    public function getAppointmentById($id)
    {
        return $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.phone as customer_phone,
            Customer.email as customer_email,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.PK_idAppointment', $id)
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0)
        ->first();
    }

    /**
     * 🔹 Lấy appointment theo Customer ID với thông tin đầy đủ
     */
    public function getAppointmentsByCustomer($customerId)
    {
        return $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.phone as customer_phone,
            Customer.email as customer_email,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.FK_idCustomer', $customerId)
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0)
        ->orderBy('Appointment.appointmentDate', 'DESC')
        ->orderBy('Appointment.appointmentTime', 'DESC')
        ->findAll();
    }

    /**
     * 🔹 Lấy appointment theo Store ID với thông tin đầy đủ
     */
    public function getAppointmentsByStore($storeId)
    {
        return $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.phone as customer_phone,
            Customer.email as customer_email,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.FK_idStore', $storeId)
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0)
        ->orderBy('Appointment.appointmentDate', 'DESC')
        ->orderBy('Appointment.appointmentTime', 'DESC')
        ->findAll();
    }

    /**
     * 🔹 Tìm kiếm appointment với thông tin đầy đủ
     */
    public function searchAppointments($filters = [])
    {
        $builder = $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.phone as customer_phone,
            Customer.email as customer_email,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0);

        // Áp dụng filters
        $searchFields = ['FK_idStore', 'FK_idCustomer', 'FK_idVehicle', 'status'];
        foreach ($searchFields as $field) {
            if (!empty($filters[$field])) {
                $builder->where("Appointment.{$field}", $filters[$field]);
            }
        }

        if (!empty($filters['appointmentDate'])) {
            $builder->where('Appointment.appointmentDate', $filters['appointmentDate']);
        }

        if (!empty($filters['appointmentDate_from']) && !empty($filters['appointmentDate_to'])) {
            $builder->where('Appointment.appointmentDate >=', $filters['appointmentDate_from'])
                    ->where('Appointment.appointmentDate <=', $filters['appointmentDate_to']);
        }

        // Tìm kiếm theo tên khách hàng
        if (!empty($filters['customer_name'])) {
            $builder->like('Customer.fullName', $filters['customer_name']);
        }

        // Tìm kiếm theo biển số xe
        if (!empty($filters['license_plate'])) {
            $builder->like('Vehicle.licensePlate', $filters['license_plate']);
        }

        return $builder->orderBy('Appointment.appointmentDate', 'DESC')
                      ->orderBy('Appointment.appointmentTime', 'DESC')
                      ->findAll();
    }

    /**
     * 🔹 Cập nhật appointment
     */
    public function updateAppointment($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * 🔹 Xóa mềm appointment
     */
    public function deleteAppointment($id)
    {
        return $this->update($id, ['deleted' => 1]);
    }

    public function getConfirmedAppointmentsByStore($storeId)
    {
        return $this->select('
                Appointment.*,
                User.fullName,
                User.phone,
                Vehicle.licensePlate,
                Vehicle.type
            ')
            ->join('User', 'User.PK_idUser = Appointment.FK_idCustomer', 'left')
            ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
            ->where('Appointment.FK_idStore', $storeId)
            ->where('Appointment.status', 'Đã xác nhận')
            ->where('Appointment.deleted', 0)
            ->orderBy('Appointment.appointmentDate', 'ASC')
            ->orderBy('Appointment.appointmentTime', 'ASC')
            ->findAll();
    }
    public function getAppointmentDetailsForEmail($appointmentId)
{
    try {
        // 🔹 Lấy thông tin chính của appointment
        $appointment = $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.email as customer_email,
            Customer.phone as customer_phone,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address,
            Store.phone as store_phone
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.PK_idAppointment', $appointmentId)
        ->where('Appointment.deleted', 0)
        ->first();
        
        if (!$appointment) {
            log_message('error', 'Không tìm thấy appointment ID: ' . $appointmentId);
            return null;
        }

        // 🔹 Lấy danh sách dịch vụ
        $db = \Config\Database::connect();
        $serviceBuilder = $db->table('appointment_service aps');
        $services = $serviceBuilder
            ->select('svc.serviceName')
            ->join('service svc', 'aps.FK_idService = svc.PK_idService', 'left')
            ->where('aps.FK_idAppointment', $appointmentId)
            ->get()
            ->getResultArray();
        
        // Tạo chuỗi tên dịch vụ
        $serviceNames = array_column($services, 'serviceName');
        $appointment['service_names'] = !empty($serviceNames) 
            ? implode(', ', $serviceNames) 
            : 'Chưa có dịch vụ';
        $appointment['status'] = $appointment['status'] ?? 'Chờ xác nhận';
        log_message('info', 'Status cho email: ' . ($appointment['status'] ?? 'NULL'));

        log_message('info', 'Lấy thông tin email thành công cho appointment: ' . $appointmentId);
        return $appointment;
        
    } catch (\Exception $e) {
        log_message('error', 'Lỗi getAppointmentDetailsForEmail: ' . $e->getMessage());
        return null;
    }
}
// public function getAllUpcomingAppointments()
// {
//     $builder = $this->db->table('appointments');
//     $builder->where('appointmentDate >=', date('Y-m-d'));
//     $builder->whereIn('status', ['Chờ xác nhận', 'Đã xác nhận']); // chỉ gửi với lịch hợp lệ
//     return $builder->get()->getResultArray();
// }
public function getAllUpcomingAppointments()
{
    $now = date('Y-m-d H:i:s');

    return $this->select('
            Appointment.*,
            Customer.fullName as customer_name,
            Customer.email as customer_email,
            Customer.phone as customer_phone,
            Vehicle.licensePlate as vehicle_license_plate,
            Vehicle.type as vehicle_type,
            Store.address as store_address,
            Store.phone as store_phone
        ')
        ->join('User as Customer', 'Customer.PK_idUser = Appointment.FK_idCustomer', 'left')
        ->join('Vehicle', 'Vehicle.PK_idVehicle = Appointment.FK_idVehicle', 'left')
        ->join('Store', 'Store.PK_idStore = Appointment.FK_idStore', 'left')
        ->where('Appointment.deleted', 0)
        ->where('Customer.deleted', 0)
        ->where('Vehicle.deleted', 0)
        ->whereIn('Appointment.status', [ 'Đã xác nhận'])
        ->where("CONCAT(Appointment.appointmentDate,' ',Appointment.appointmentTime) >= ", $now)
        ->orderBy('Appointment.appointmentDate', 'ASC')
        ->orderBy('Appointment.appointmentTime', 'ASC')
        ->findAll();
}

    /**
     * 🔹 Đếm số lượng lịch hẹn tại cửa hàng theo trạng thái
     */
        /**
     * 🔹 Đếm số lượng lịch hẹn theo cửa hàng + ngày + giờ
     */
    public function countAppointmentsByStoreAndTime($storeId, $date = null, $time = null)
    {
        $db = \Config\Database::connect();

        $builder = $db->table($this->table);
        $builder->where('FK_idStore', $storeId);
        $builder->where('deleted', 0);

        if ($date) {
            $builder->where('appointmentDate', $date);
        }

        if ($time) {
            // Nếu bạn chỉ muốn đếm đúng khung giờ, ví dụ 10:00–11:00
            $builder->where('appointmentTime', $time);
        }

        // Đếm số "Chờ xác nhận"
        $pendingQuery = clone $builder;
        $pending = $pendingQuery->where('status', 'Chờ xác nhận')->countAllResults();

        // Đếm số "Đã xác nhận"
        $confirmedQuery = clone $builder;
        $confirmed = $confirmedQuery->where('status', 'Đã xác nhận')->countAllResults();

        return [
            'storeId'   => $storeId,
            'date'      => $date,
            'time'      => $time,
            'pending'   => $pending,
            'confirmed' => $confirmed,
            'total'     => $pending + $confirmed
        ];
    }


}
