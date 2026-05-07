<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class CEmail extends ResourceController
{
    use ResponseTrait;

    /**
     * 🔹 Gửi email thông báo đặt lịch thành công
     */
    public function sendAppointmentConfirmation($appointmentId)
{
    try {
        log_message('info', '📧 Bắt đầu gửi email xác nhận cho appointment: ' . $appointmentId);
        
        // Lấy thông tin lịch hẹn
        $appointmentModel = new \App\Models\MAppointment();
        $appointment = $appointmentModel->getAppointmentDetailsForEmail($appointmentId);
        
        if (!$appointment) {
            log_message('error', '❌ Không tìm thấy appointment ID: ' . $appointmentId);
            return $this->failNotFound('Không tìm thấy thông tin lịch hẹn');
        }

        // Kiểm tra email khách hàng
        if (empty($appointment['customer_email'])) {
            log_message('warning', '⚠️ Khách hàng không có email: ' . $appointmentId);
            return $this->fail('Khách hàng không có email');
        }

        log_message('info', '📨 Gửi email đến: ' . $appointment['customer_email']);

        $emailData = [
            'type' => 'appointment_confirmation',
            'to' => $appointment['customer_email'],
            'subject' => 'Xác nhận đặt lịch sửa xe - Sửa xe 247',
            'data' => $appointment
        ];

        $result = $this->sendEmail($emailData);

        if ($result) {
            log_message('info', '✅ Gửi email thành công cho appointment: ' . $appointmentId);
            return $this->respond([
                'status' => 'success',
                'message' => 'Đã gửi email xác nhận đặt lịch thành công'
            ]);
        } else {
            log_message('error', '❌ Gửi email thất bại cho appointment: ' . $appointmentId);
            return $this->fail('Gửi email thất bại');
        }

    } catch (\Exception $e) {
        log_message('error', '💥 Exception khi gửi email: ' . $e->getMessage());
        return $this->failServerError('Lỗi khi gửi email: ' . $e->getMessage());
    }
}

    /**
     * 🔹 Gửi email thông báo thay đổi trạng thái lịch hẹn
     */
    public function sendStatusUpdate($appointmentId)
    {
        try {
            log_message('info', '📧 [STATUS_UPDATE] Bắt đầu gửi email cập nhật trạng thái cho appointment: ' . $appointmentId);
            
            $appointmentModel = new \App\Models\MAppointment();
            $appointment = $appointmentModel->getAppointmentDetailsForEmail($appointmentId);
            
            if (!$appointment) {
                log_message('error', '❌ [STATUS_UPDATE] Không tìm thấy appointment ID: ' . $appointmentId);
                return $this->failNotFound('Không tìm thấy thông tin lịch hẹn');
            }

            log_message('info', '📋 [STATUS_UPDATE] Trạng thái mới: ' . $appointment['status']);

            if (empty($appointment['customer_email'])) {
                log_message('warning', '⚠️ [STATUS_UPDATE] Khách hàng không có email - Appointment: ' . $appointmentId);
                return $this->fail('Khách hàng không có email');
            }

            $statusMessages = [
                'Chờ xác nhận' => 'Lịch hẹn của bạn đang chờ xác nhận',
                'Đã xác nhận' => 'Lịch hẹn của bạn đã được xác nhận',
                'Đã hủy' => 'Lịch hẹn của bạn đã bị hủy',
                'Hoàn thành' => 'Lịch hẹn của bạn đã hoàn thành'
            ];

            log_message('info', '📨 [STATUS_UPDATE] Gửi email đến: ' . $appointment['customer_email']);

            $emailData = [
                'type' => 'status_update',
                'to' => $appointment['customer_email'],
                'subject' => 'Cập nhật trạng thái lịch hẹn - Sửa xe 247',
                'data' => array_merge($appointment, [
                    'status_message' => $statusMessages[$appointment['status']] ?? 'Trạng thái lịch hẹn đã được cập nhật'
                ])
            ];

            $result = $this->sendEmail($emailData);

            if ($result) {
                log_message('info', '✅ [STATUS_UPDATE] Gửi email thành công cho appointment: ' . $appointmentId);
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Đã gửi email cập nhật trạng thái thành công'
                ]);
            } else {
                log_message('error', '❌ [STATUS_UPDATE] Gửi email thất bại cho appointment: ' . $appointmentId);
                return $this->fail('Gửi email thất bại');
            }

        } catch (\Exception $e) {
            log_message('error', '💥 [STATUS_UPDATE] Exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->failServerError('Lỗi khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Gửi email nhắc lịch hẹn (1 giờ trước)
     */
    public function sendAppointmentReminder($appointmentId)
    {
        try {
            log_message('info', '📧 [REMINDER] Bắt đầu gửi email nhắc lịch cho appointment: ' . $appointmentId);
            
            $appointmentModel = new \App\Models\MAppointment();
            $appointment = $appointmentModel->getAppointmentDetailsForEmail($appointmentId);
            
            if (!$appointment) {
                log_message('error', '❌ [REMINDER] Không tìm thấy appointment ID: ' . $appointmentId);
                return $this->failNotFound('Không tìm thấy thông tin lịch hẹn');
            }

            log_message('info', '📋 [REMINDER] Lịch hẹn lúc: ' . $appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);

            if (empty($appointment['customer_email'])) {
                log_message('warning', '⚠️ [REMINDER] Khách hàng không có email - Appointment: ' . $appointmentId);
                return $this->fail('Khách hàng không có email');
            }

            log_message('info', '📨 [REMINDER] Gửi email đến: ' . $appointment['customer_email']);

            $emailData = [
                'type' => 'appointment_reminder',
                'to' => $appointment['customer_email'],
                'subject' => 'Nhắc lịch hẹn sửa xe - Sửa xe 247',
                'data' => $appointment
            ];

            $result = $this->sendEmail($emailData);

            if ($result) {
                log_message('info', '✅ [REMINDER] Gửi email thành công cho appointment: ' . $appointmentId);
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Đã gửi email nhắc lịch thành công'
                ]);
            } else {
                log_message('error', '❌ [REMINDER] Gửi email thất bại cho appointment: ' . $appointmentId);
                return $this->fail('Gửi email thất bại');
            }

        } catch (\Exception $e) {
            log_message('error', '💥 [REMINDER] Exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->failServerError('Lỗi khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Gửi email thông báo hủy lịch hẹn
     */
    public function sendCancellationNotification($appointmentId)
    {
        try {
            log_message('info', '📧 [CANCELLATION] Bắt đầu gửi email hủy lịch cho appointment: ' . $appointmentId);
            
            $appointmentModel = new \App\Models\MAppointment();
            $appointment = $appointmentModel->getAppointmentDetailsForEmail($appointmentId);
            
            if (!$appointment) {
                log_message('error', '❌ [CANCELLATION] Không tìm thấy appointment ID: ' . $appointmentId);
                return $this->failNotFound('Không tìm thấy thông tin lịch hẹn');
            }

            log_message('info', '📋 [CANCELLATION] Lịch đã hủy: ' . $appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);

            if (empty($appointment['customer_email'])) {
                log_message('warning', '⚠️ [CANCELLATION] Khách hàng không có email - Appointment: ' . $appointmentId);
                return $this->fail('Khách hàng không có email');
            }

            log_message('info', '📨 [CANCELLATION] Gửi email đến: ' . $appointment['customer_email']);

            $emailData = [
                'type' => 'cancellation',
                'to' => $appointment['customer_email'],
                'subject' => 'Thông báo hủy lịch hẹn - Sửa xe 247',
                'data' => $appointment
            ];

            $result = $this->sendEmail($emailData);

            if ($result) {
                log_message('info', '✅ [CANCELLATION] Gửi email thành công cho appointment: ' . $appointmentId);
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Đã gửi email thông báo hủy lịch thành công'
                ]);
            } else {
                log_message('error', '❌ [CANCELLATION] Gửi email thất bại cho appointment: ' . $appointmentId);
                return $this->fail('Gửi email thất bại');
            }

        } catch (\Exception $e) {
            log_message('error', '💥 [CANCELLATION] Exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->failServerError('Lỗi khi gửi email: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Core function để gửi email
     */
    private function sendEmail($emailData)
    {
        try {
            log_message('info', '🔧 [SEND_EMAIL] Chuẩn bị gửi email đến: ' . $emailData['to']);
            
            $email = Services::email();

            // Template email dựa trên type
            $emailContent = $this->generateEmailContent($emailData);

            $email->setTo($emailData['to']);
            $email->setFrom('thanhtung220504@gmail.com', 'Sửa xe 247');
            $email->setSubject($emailData['subject']);
            $email->setMessage($emailContent);

            log_message('info', '📤 [SEND_EMAIL] Đang gửi email...');

            if ($email->send()) {
                log_message('info', '✅ [SEND_EMAIL] Email sent successfully to: ' . $emailData['to']);
                return true;
            } else {
                $debugInfo = $email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', '❌ [SEND_EMAIL] Email sending failed. Debug info: ' . $debugInfo);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', '💥 [SEND_EMAIL] Exception in sendEmail: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * 🔹 Tạo nội dung email dựa trên loại thông báo
     */
    private function generateEmailContent($emailData)
    {
        $data = $emailData['data'];
        
        log_message('info', '📝 [GENERATE_EMAIL] Tạo template email cho type: ' . $emailData['type']);
        
        switch ($emailData['type']) {
            case 'appointment_confirmation':
                return $this->confirmationTemplate($data);
            
            case 'status_update':
                return $this->statusUpdateTemplate($data);
            
            case 'appointment_reminder':
                return $this->reminderTemplate($data);
            
            case 'cancellation':
                return $this->cancellationTemplate($data);
            
            default:
                log_message('warning', '⚠️ [GENERATE_EMAIL] Unknown email type: ' . $emailData['type']);
                return $this->defaultTemplate($data);
        }
    }

    /**
     * 🔹 Template email xác nhận đặt lịch
     */
    private function confirmationTemplate($data)
    {
        $appointmentDateTime = date('H:i d/m/Y', strtotime($data['appointmentDate'] . ' ' . $data['appointmentTime']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Xác nhận đặt lịch</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .footer { background: #343a40; color: white; padding: 10px; text-align: center; }
                .info-item { margin-bottom: 10px; }
                .status { color: #28a745; font-weight: bold; }
                .service-list { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ĐẶT LỊCH THÀNH CÔNG</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$data['customer_name']}</strong>,</p>
                    <p>Cảm ơn bạn đã đặt lịch sửa xe tại Sửa xe 247. Dưới đây là thông tin lịch hẹn của bạn:</p>

                    <div class='info-item'><strong>Địa chỉ:</strong> {$data['store_address']}</div>
                    <div class='info-item'><strong>Thời gian:</strong> {$appointmentDateTime}</div>
                    <div class='info-item'><strong>Xe:</strong> {$data['vehicle_license_plate']} - {$data['vehicle_type']}</div>
                    
                    <div class='service-list'>
                        <strong>Dịch vụ đã đặt:</strong><br>
                        {$data['service_names']}
                    </div>
                    
                    <div class='info-item'><strong>Trạng thái:</strong> <span class='status'>Chờ xác nhận</span></div>
                    
                    <p style='margin-top: 20px;'>
                        <strong>Lưu ý:</strong> Vui lòng đến đúng giờ. Nếu có thay đổi, xin vui lòng liên hệ trước.
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2024 Sửa xe 247. All rights reserved.</p>
                    <p>Hotline: {$data['store_phone']} | Email: thanhtung220504@gmail.com</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * 🔹 Template email cập nhật trạng thái
     */
    private function statusUpdateTemplate($data)
    {
        $appointmentDateTime = date('H:i d/m/Y', strtotime($data['appointmentDate'] . ' ' . $data['appointmentTime']));
        $statusColor = $this->getStatusColor($data['status']);
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Cập nhật trạng thái</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #17a2b8; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .footer { background: #343a40; color: white; padding: 10px; text-align: center; }
                .info-item { margin-bottom: 10px; }
                .status { font-weight: bold; padding: 5px 10px; border-radius: 3px; }
                .service-list { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>CẬP NHẬT TRẠNG THÁI LỊCH HẸN</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$data['customer_name']}</strong>,</p>
                    <p>{$data['status_message']}</p>
                    
                    <div class='info-item'><strong>Cửa hàng:</strong> {$data['store_address']}</div>
                    <div class='info-item'><strong>Thời gian:</strong> {$appointmentDateTime}</div>
                    <div class='info-item'><strong>Xe:</strong> {$data['vehicle_license_plate']} - {$data['vehicle_type']}</div>
                    
                    <div class='service-list'>
                        <strong>Dịch vụ:</strong><br>
                        {$data['service_names']}
                    </div>
                    
                    <div class='info-item'>
                        <strong>Trạng thái mới:</strong> 
                        <span class='status' style='color: {$statusColor};'>{$data['status']}</span>
                    </div>
                </div>
                <div style='background:#343a40;color:#ffffff;padding:10px;text-align:center;'>
                    <p style='margin:0;'>© 2024 Sửa xe 247. All rights reserved.</p>
                    <p style='margin:0;'>Hotline: {$data['store_phone']} | Email: thanhtung220504@gmail.com</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * 🔹 Template email nhắc lịch hẹn
     */
    private function reminderTemplate($data)
    {
        $appointmentDateTime = date('H:i d/m/Y', strtotime($data['appointmentDate'] . ' ' . $data['appointmentTime']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Nhắc lịch hẹn</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ffc107; color: #212529; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .footer { background: #343a40; color: white; padding: 10px; text-align: center; }
                .info-item { margin-bottom: 10px; }
                .reminder { background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; }
                .service-list { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>NHẮC LỊCH HẸN SỬA XE</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$data['customer_name']}</strong>,</p>
                    
                    <div class='reminder'>
                        <h3> Bạn có lịch hẹn sửa xe trong 1 giờ tới:</h3>
                        <div class='info-item'><strong>Cửa hàng:</strong> {$data['store_address']}</div>
                        <div class='info-item'><strong>Thời gian:</strong> {$appointmentDateTime}</div>
                        <div class='info-item'><strong>Xe:</strong> {$data['vehicle_license_plate']} - {$data['vehicle_type']}</div>
                        
                        <div class='service-list'>
                            <strong>Dịch vụ đã đặt:</strong><br>
                            {$data['service_names']}
                        </div>
                    </div>
                    
                    <p style='margin-top: 20px;'>
                        <strong>Vui lòng đến đúng giờ để được phục vụ tốt nhất.</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2024 Sửa xe 247. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * 🔹 Template email hủy lịch hẹn
     */
    private function cancellationTemplate($data)
    {
        $appointmentDateTime = date('H:i d/m/Y', strtotime($data['appointmentDate'] . ' ' . $data['appointmentTime']));
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Hủy lịch hẹn</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .footer { background: #343a40; color: white; padding: 10px; text-align: center; }
                .info-item { margin-bottom: 10px; }
                .service-list { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>LỊCH HẸN ĐÃ BỊ HỦY</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>{$data['customer_name']}</strong>,</p>
                    <p>Lịch hẹn sửa xe của bạn đã bị hủy. Dưới đây là thông tin lịch hẹn đã hủy:</p>
                    
                    <div class='info-item'><strong>Cửa hàng:</strong> {$data['store_address']}</div>
                    <div class='info-item'><strong>Thời gian:</strong> {$appointmentDateTime}</div>
                    <div class='info-item'><strong>Xe:</strong> {$data['vehicle_license_plate']} - {$data['vehicle_type']}</div>
                    
                    <div class='service-list'>
                        <strong>Dịch vụ đã đặt:</strong><br>
                        {$data['service_names']}
                    </div>
                    
                    <p style='margin-top: 20px;'>
                        Nếu bạn muốn đặt lịch mới, vui lòng truy cập website hoặc ứng dụng của chúng tôi.
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2024 Sửa xe 247. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * 🔹 Template mặc định
     */
    private function defaultTemplate($data)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Thông báo từ Sửa xe 247</title>
        </head>
        <body>
            <h2>Thông báo từ Sửa xe 247</h2>
            <p>Xin chào {$data['customer_name']},</p>
            <p>Đây là thông báo về lịch hẹn của bạn.</p>
        </body>
        </html>
        ";
    }

    /**
     * 🔹 Lấy màu sắc cho trạng thái
     */
    private function getStatusColor($status)
    {
        $colors = [
            'Chờ xác nhận' => '#ffc107',
            'Đã xác nhận' => '#28a745',
            'Đã hủy' => '#dc3545',
            'Hoàn thành' => '#17a2b8'
        ];
        
        return $colors[$status] ?? '#6c757d';
    }

    /**
     * 🔹 API để frontend gọi gửi email
     */
    public function sendEmailNotification()
    {
        try {
            $request = $this->request->getJSON(true);
            
            log_message('info', '🌐 [API] Nhận request gửi email: ' . json_encode($request));
            
            $type = $request['type'] ?? '';
            $appointmentId = $request['appointmentId'] ?? null;
            
            if (!$appointmentId) {
                log_message('error', '❌ [API] Thiếu appointmentId trong request');
                return $this->failValidationError('Thiếu appointmentId');
            }

            log_message('info', '🔄 [API] Xử lý type: ' . $type . ' cho appointment: ' . $appointmentId);

            switch ($type) {
                case 'confirmation':
                    return $this->sendAppointmentConfirmation($appointmentId);
                
                case 'status_update':
                    return $this->sendStatusUpdate($appointmentId);
                
                case 'reminder':
                    return $this->sendAppointmentReminder($appointmentId);
                
                case 'cancellation':
                    return $this->sendCancellationNotification($appointmentId);
                
                default:
                    log_message('error', '❌ [API] Loại email không hợp lệ: ' . $type);
                    return $this->failValidationError('Loại email không hợp lệ');
            }
        } catch (\Exception $e) {
            log_message('error', '💥 [API] Exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->failServerError('Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Test gửi email
     */
    public function testEmail($appointmentId = null)
    {
        try {
            log_message('info', '🧪 [TEST] Bắt đầu test email với appointmentId: ' . $appointmentId);
            
            if (!$appointmentId) {
                log_message('error', '❌ [TEST] Thiếu appointmentId');
                return $this->failValidationError('Thiếu appointmentId');
            }

            $result = $this->sendAppointmentConfirmation($appointmentId);
            
            if ($result->getStatusCode() === 200) {
                log_message('info', '✅ [TEST] Test email thành công!');
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Test email thành công!'
                ]);
            } else {
                log_message('error', '❌ [TEST] Test email thất bại');
                return $this->fail('Test email thất bại');
            }

        } catch (\Exception $e) {
            log_message('error', '💥 [TEST] Exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->failServerError('Lỗi test email: ' . $e->getMessage());
        }
    }
    /**
 * 🔹 Kiểm tra tất cả lịch hẹn và gửi email nếu còn < 1 giờ
 * 👉 API: GET /api/email/check-reminders
 */
public function checkUpcomingAppointments()
{
    try {
        log_message('info', '⏰ [REMINDER_CHECK] Bắt đầu kiểm tra các lịch hẹn sắp tới...');

        $appointmentModel = new \App\Models\MAppointment();
        $now = new \DateTime('now');
        $appointments = $appointmentModel->getAllUpcomingAppointments(); // lấy tất cả lịch trong tương lai gần

        $countSent = 0;

        foreach ($appointments as $appointment) {
            // Kết hợp ngày + giờ
            $appointmentDateTime = new \DateTime($appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);
            $diffInSeconds = $appointmentDateTime->getTimestamp() - $now->getTimestamp();

            // Nếu còn < 1 tiếng (3600s) và > 0 (chưa tới lịch)
            if ($diffInSeconds > 0 && $diffInSeconds <= 3600) {
                log_message('info', '🔔 [REMINDER_CHECK] Gửi mail nhắc lịch cho ID: ' . $appointment['PK_idAppointment']);
                $this->sendAppointmentReminder($appointment['PK_idAppointment']);
                $countSent++;
            }
        }

        return $this->respond([
            'status' => 'success',
            'message' => "Đã gửi $countSent email nhắc lịch.",
            'sent_count' => $countSent
        ]);

    } catch (\Exception $e) {
        log_message('error', '💥 [REMINDER_CHECK] Exception: ' . $e->getMessage());
        return $this->failServerError('Lỗi khi kiểm tra lịch nhắc: ' . $e->getMessage());
    }
}
/**
 * 🔹 Cron Job: Kiểm tra lịch hẹn sắp đến và gửi mail nhắc nhở
 * 👉 Chạy: php public/index.php CEmail runReminderCron
 * 👉 Hoặc: http://localhost/suaxe247/public/index.php/CEmail/runReminderCron
 */
public function runReminderCron()
{
    try {
        log_message('info', '⏰ [CRON] Bắt đầu chạy Cron kiểm tra lịch sắp tới...');

        $appointmentModel = new \App\Models\MAppointment();
        $now = new \DateTime('now');

        // 🕓 Thay đổi dễ dàng cho mục đích test
        $reminderWindowSeconds = 36000; // 3600 giây = 1 giờ
        // ví dụ: 600 = 10 phút, 1800 = 30 phút

        // 🔹 Lấy danh sách các lịch hẹn sắp tới
        $appointments = $appointmentModel->getAllUpcomingAppointments();

        $countSent = 0;

        foreach ($appointments as $appointment) {
            $appointmentDateTime = new \DateTime($appointment['appointmentDate'] . ' ' . $appointment['appointmentTime']);
            $diffInSeconds = $appointmentDateTime->getTimestamp() - $now->getTimestamp();

            // Nếu còn < thời gian nhắc (1h mặc định) và > 0 (chưa đến)
            if ($diffInSeconds > 0 && $diffInSeconds <= $reminderWindowSeconds) {
                log_message('info', '🔔 [CRON] Gửi mail nhắc lịch cho ID: ' . $appointment['PK_idAppointment']);
                $this->sendAppointmentReminder($appointment['PK_idAppointment']);
                $countSent++;
            }
        }

        log_message('info', "✅ [CRON] Đã gửi {$countSent} email nhắc lịch.");
        return $this->respond([
            'status' => 'success',
            'message' => "Đã gửi {$countSent} email nhắc lịch tự động."
        ]);

    } catch (\Exception $e) {
        log_message('error', '💥 [CRON] Lỗi Cron: ' . $e->getMessage());
        return $this->failServerError('Cron lỗi: ' . $e->getMessage());
    }
}

}