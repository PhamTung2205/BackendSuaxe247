<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MReport;

class CReport extends ResourceController
{
    protected $format = 'json';
    protected $reportModel;

    public function __construct()
    {
        $this->reportModel = new MReport();
    }

    /**
     * Lấy báo cáo xuất nhập kho trong khoảng thời gian
     */
    public function warehouseReport()
    {
        // --- Lấy thông tin ngày và storeId từ query string ---
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');
        $storeId = $this->request->getGet('storeId'); // <-- thêm dòng này

        // --- Kiểm tra dữ liệu đầu vào ---
        if (empty($from) || empty($to)) {
            return $this->failValidationErrors('Vui lòng nhập đầy đủ thời gian "từ ngày" và "đến ngày"');
        }

        // --- Kiểm tra đăng nhập ---
        $user = session()->get('user');
        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        try {
            // --- Truyền storeId xuống model ---
            $reportData = $this->reportModel->getWarehouseReport($from, $to, $storeId);

            if (isset($reportData['error'])) {
                return $this->failServerError($reportData['error']);
            }

            // --- Trả kết quả ---
            return $this->respond([
                'status'  => 'success',
                'message' => 'Báo cáo xuất nhập kho từ ' . $from . ' đến ' . $to,
                'data'    => $reportData
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Lỗi khi tạo báo cáo xuất nhập kho: ' . $e->getMessage());
            return $this->failServerError('Không thể tạo báo cáo xuất nhập kho');
        }
    }



    /**
     * (Tùy chọn) Xuất báo cáo ra file Excel — nếu bạn muốn hỗ trợ sau này
     */
    public function exportInventory()
    {
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');

        if (empty($from) || empty($to)) {
            return $this->failValidationErrors('Thiếu thời gian xuất báo cáo');
        }

        $reportData = $this->reportModel->getWarehouseReport($from, $to);
        if (isset($reportData['error'])) {
            return $this->failServerError($reportData['error']);
        }

        // Gợi ý: bạn có thể dùng thư viện PhpSpreadsheet để xuất Excel tại đây
        return $this->respond([
            'status' => 'success',
            'message' => 'Xuất báo cáo thành công (giả lập)',
            'data' => $reportData
        ]);
    }

    /**
     * Lấy báo cáo hoạt động cửa hàng trong khoảng thời gian
     */
    public function storeReport()
    {
        // --- Lấy dữ liệu từ query string ---
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');
        $storeId = $this->request->getGet('storeId');

        // --- Kiểm tra dữ liệu đầu vào ---
        if (empty($from) || empty($to)) {
            return $this->failValidationErrors('Vui lòng nhập đầy đủ thời gian "từ ngày" và "đến ngày"');
        }

        // --- Kiểm tra đăng nhập ---
        $user = session()->get('user');
        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        try {
            // ✅ Truyền thêm $storeId xuống model
            $reportData = $this->reportModel->getStoreReport($from, $to, $storeId);

            if (isset($reportData['error'])) {
                return $this->failServerError($reportData['error']);
            }

            // --- Trả kết quả ---
            return $this->respond([
                'status'  => 'success',
                'message' => "Báo cáo hoạt động cửa hàng từ $from đến $to",
                'data'    => $reportData
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Lỗi khi tạo báo cáo cửa hàng: ' . $e->getMessage());
            return $this->failServerError('Không thể tạo báo cáo cửa hàng');
        }
    }


    /**
     * (Tùy chọn) Xuất báo cáo hoạt động cửa hàng ra file Excel
     */
    public function exportStoreReport()
    {
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');

        if (empty($from) || empty($to)) {
            return $this->failValidationErrors('Thiếu thời gian xuất báo cáo');
        }

        $reportData = $this->reportModel->getStoreReport($from, $to);
        if (isset($reportData['error'])) {
            return $this->failServerError($reportData['error']);
        }

        // Gợi ý: dùng PhpSpreadsheet để xuất Excel
        return $this->respond([
            'status'  => 'success',
            'message' => 'Xuất báo cáo cửa hàng thành công (giả lập)',
            'data'    => $reportData
        ]);
    }
}
