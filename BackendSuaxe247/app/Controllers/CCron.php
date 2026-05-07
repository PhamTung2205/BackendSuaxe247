<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CCron extends Controller
{
    /**
     * 🔹 Gửi email nhắc lịch cho các appointment trong khoảng thời gian tới
     * @param int $minutesAhead Số phút trước thời điểm hiện tại để nhắc (mặc định 60 phút)
     * Chạy cron mỗi 10 phút:
     * /*10 * * * * php /path/to/index.php cron sendReminders
     */
    public function sendReminders($minutesAhead = 60)
    {
        try {
            $appointmentModel = new \App\Models\MAppointment();
            $emailController = new \App\Controllers\CEmail();

            // Thời gian hiện tại
            $now = new \DateTime();
            $nowStr = $now->format('Y-m-d H:i:00');

            // Thời gian nhắc trước $minutesAhead phút
            $remindTime = clone $now;
            $remindTime->modify("+{$minutesAhead} minutes");
            $remindTimeStr = $remindTime->format('Y-m-d H:i:00');

            log_message('info', "⏰ [CRON] Kiểm tra lịch hẹn từ $nowStr đến $remindTimeStr (nhắc trước $minutesAhead phút)");

            // Lấy các appointment trong khoảng thời gian
            $db = \Config\Database::connect();
            $builder = $db->table('Appointment');

            $appointments = $builder
                ->select('PK_idAppointment, appointmentDate, appointmentTime, status')
                ->where("CONCAT(appointmentDate, ' ', appointmentTime) >=", $nowStr)
                ->where("CONCAT(appointmentDate, ' ', appointmentTime) <=", $remindTimeStr)
                ->where('status', 'Đã xác nhận') // Chỉ gửi cho lịch đã xác nhận
                ->where('deleted', 0)
                ->get()
                ->getResultArray();

            log_message('info', "📋 Tìm thấy " . count($appointments) . " lịch hẹn cần nhắc");

            $sentCount = 0;
            foreach ($appointments as $appointment) {
                try {
                    $result = $emailController->sendAppointmentReminder($appointment['PK_idAppointment']);

                    if ($result->getStatusCode() === 200) {
                        $sentCount++;
                        log_message('info', "✅ Đã gửi reminder cho appointment: {$appointment['PK_idAppointment']} lúc {$appointment['appointmentDate']} {$appointment['appointmentTime']}");
                    } else {
                        log_message('error', "❌ Gửi thất bại cho appointment: {$appointment['PK_idAppointment']}");
                    }
                } catch (\Exception $e) {
                    log_message('error', "❌ Lỗi gửi reminder cho appointment {$appointment['PK_idAppointment']}: " . $e->getMessage());
                }
            }

            $summary = "📧 Tổng kết: Đã gửi $sentCount/" . count($appointments) . " email nhắc lịch";
            log_message('info', $summary);

            echo json_encode([
                'status' => 'success',
                'message' => $summary,
                'total' => count($appointments),
                'sent' => $sentCount
            ]);

        } catch (\Exception $e) {
            log_message('error', '💥 Lỗi cron sendReminders: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
