<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MAppointment;

class MVehicle extends Model
{
    protected $table = 'Vehicle'; // Tên bảng
    protected $primaryKey = 'PK_idVehicle';
    protected $useAutoIncrement = true;  // Khóa chính
    protected $allowedFields = [
        'PK_idVehicle',
        'FK_idUser',
        'licensePlate',
        'type',
        'created',
        'updated',
        'deleted'
    ]; // Các cột được phép thao tác

    // Tự động cập nhật created, updated
    protected $useTimestamps = true;
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';

    // Không dùng soft delete mặc định của CI4, mà quản lý deleted = 0/1 thủ công
    protected $deletedField = 'deleted';

    public function getAllVehicle()
    {
        return $this->findAll(); // Lấy tất cả dữ liệu
    }

    public function getVehiclesByUserId($userId, $limit = 6, $offset = 0)
{
    $builder = $this->select('
            Vehicle.PK_idVehicle,
            Vehicle.licensePlate,
            Vehicle.type,
            (
                SELECT COUNT(*) 
                FROM Appointment 
                WHERE Appointment.FK_idVehicle = Vehicle.PK_idVehicle
                AND Appointment.status IN ("Đã xác nhận", "Chờ xác nhận")
                AND Appointment.deleted = 0
            ) AS hasConfirmedAppointment
        ')
        ->where('Vehicle.FK_idUser', $userId)
        ->where('Vehicle.deleted', 0)
        ->limit($limit, $offset);

    $data = $builder->get()->getResultArray();

    // Đếm tổng số xe (để FE biết tổng số trang)
    $total = $this->where('FK_idUser', $userId)
                  ->where('deleted', 0)
                  ->countAllResults();

    return [
        'data' => $data,
        'total' => $total
    ];
}



    public function getVehicleByIdAndUser($vehicleId, $userId)
    {
        return $this->where('PK_idVehicle', $vehicleId)
                    ->where('FK_idUser', $userId)
                    ->where('deleted', 0)
                    ->first();
    }

   public function getVehicleById($vehicleId)
    {
        return $this->where('PK_idVehicle', $vehicleId)
                    ->where('deleted', 0)
                    ->first();
    }
    // Thêm xe mới
    public function addVehicle($data)
    {
        return $this->insert($data);
    }

    // Sửa xe
    public function updateVehicle($vehicleId, $data)
    {
        return $this->update($vehicleId, $data);
    }

    // Xóa xe (soft delete)
    public function deleteVehicle($vehicleId)
    {
        $appointmentModel = new MAppointment();

        // Kiểm tra xem xe có lịch hẹn đang hoạt động không
        $hasActiveAppointment = $appointmentModel
            ->where('FK_idVehicle', $vehicleId)
            ->whereIn('status', ['Đã xác nhận', 'Chờ xác nhận'])
            ->where('deleted', 0)
            ->first();

        if ($hasActiveAppointment) { 
            return [
                'status'  => 'error',
                'message' => 'Không thể xóa xe vì đang có lịch hẹn.'
            ];
        }

        // Nếu không có, tiến hành soft delete
        $this->update($vehicleId, ['deleted' => 1]);

        return [
            'status'  => 'success',
            'message' => 'Đã xóa xe thành công.'
        ];
    }

    public function getAllVehiclesByUserId($userId)
{
    return $this->select('
            Vehicle.PK_idVehicle,
            Vehicle.licensePlate,
            Vehicle.type,
            (
                SELECT COUNT(*) 
                FROM Appointment 
                WHERE Appointment.FK_idVehicle = Vehicle.PK_idVehicle
                AND Appointment.status IN ("Đã xác nhận", "Chờ xác nhận")
                AND Appointment.deleted = 0
            ) AS hasConfirmedAppointment
        ')
        ->where('Vehicle.FK_idUser', $userId)
        ->where('Vehicle.deleted', 0)
        ->orderBy('Vehicle.PK_idVehicle', 'DESC')
        ->findAll();
}
}


