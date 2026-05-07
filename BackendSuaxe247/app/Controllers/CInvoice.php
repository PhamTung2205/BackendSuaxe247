<?php

namespace App\Controllers;

use App\Models\MInvoice;
use App\Models\MInvoiceService;
use CodeIgniter\RESTful\ResourceController;

class CInvoice extends ResourceController
{
    protected $format = 'json';

    protected $invoiceModel;
    protected $invoiceServiceModel;

    public function __construct()
    {
        $this->invoiceModel = new MInvoice();
        $this->invoiceServiceModel = new MInvoiceService();
    }

    public function index()
    {
        $storeId = $this->request->getVar('storeId');
        $search = $this->request->getVar('searchName');
        $invoiceId = $this->request->getVar('invoiceId');
        $dateFrom = $this->request->getVar('dateFrom');
        $dateTo = $this->request->getVar('dateTo');

        $page = (int)$this->request->getVar('page') ?: 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'search' => $search,
            'invoice_id' => $invoiceId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'store_id' => $storeId,
        ];

        $userSession = session()->get('user');

        try {
            $allInvoices = $this->invoiceModel->getAllInvoice($filters, $userSession);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        $total = count($allInvoices);
        $invoices = array_slice($allInvoices, $offset, $perPage);

        return $this->respond([
            'status' => 'success',
            'data' => $invoices,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'message' => 'Danh sách hóa đơn'
        ]);
    }


    public function show($id = null)
    {
        if (!$id) {
            return $this->failValidationError('Chưa truyền ID hóa đơn');
        }

        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            return $this->failNotFound('Không tìm thấy hóa đơn');
        }

        return $this->respond([
            'status'  => 'success',
            'data'    => $invoice,
            'message' => 'Chi tiết hóa đơn'
        ]);
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->failValidationError('Chưa truyền ID hóa đơn');
        }

        try {
            // Kiểm tra hóa đơn có tồn tại không
            $invoice = $this->invoiceModel->find($id);
            if (!$invoice || $invoice['deleted'] == 1) {
                return $this->failNotFound('Hóa đơn không tồn tại hoặc đã bị xóa');
            }

            // Gọi model để set deleted = 1
            $deleted = $this->invoiceModel->deleteInvoice($id);

            if ($deleted) {
                return $this->respond([
                    'status'  => 'success',
                    'message' => 'Xóa hóa đơn thành công'
                ]);
            } else {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Không thể xóa hóa đơn'
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function create()
    {
        $data = $this->request->getJSON(true);
        log_message('debug', 'Data from FE: ' . json_encode($data));

        if (!$data) {
            return $this->failValidationError('Dữ liệu không hợp lệ');
        }

        // Validate các trường bắt buộc
        $requiredFields = ['phone', 'customerName', 'vehicle_license', 'vehicle_type', 'checkInTime', 'services'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->failValidationError("Thiếu trường bắt buộc: $field");
            }
        }

        // Kiểm tra services là mảng và không rỗng
        if (!is_array($data['services']) || empty($data['services'])) {
            return $this->failValidationError("Trường services phải là mảng và không rỗng");
        }

        if (empty($data['FK_idAppointment'])) {
            $data['FK_idAppointment'] = null;
        }

        try {
            $result = $this->invoiceModel->addInvoice($data);

            // Nếu status = false, trả lỗi cho FE
            if (!$result['status']) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => $result['message']
                ], 400); // 400 vì lỗi dữ liệu (ví dụ tồn kho không đủ)
            }

            $invoiceId = $result['invoiceId'];
            $invoice = $this->invoiceModel->getInvoiceById($invoiceId);

            return $this->respond([
                'status'  => 'success',
                'data'    => $invoice,
                'message' => 'Tạo hóa đơn thành công'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Invoice create error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return $this->respond([
                'status'  => 'error',
                'message' => 'Tạo hóa đơn thất bại',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function userInvoices()
    {
        $page = (int)$this->request->getVar('page') ?: 1;
        $perPage = 6;
        $user = session()->get('user');

        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        try {
            $filters = [
                'search'      => $this->request->getGet('search'),
                'date_from'   => $this->request->getGet('date_from'),
                'date_to'     => $this->request->getGet('date_to'),
                'cost_range'  => $this->request->getGet('cost_range'),
            ];

            $result = $this->invoiceModel->getInvoicesByCurrentUser($filters, $page, $perPage);

            return $this->respond([
                'status'  => 'success',
                'data'    => $result, // đã có 'data' và 'pagination'
                'message' => 'Danh sách hóa đơn người dùng hiện tại',
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Lỗi khi lấy danh sách hóa đơn khách hàng: ' . $e->getMessage());
            return $this->failServerError('Không thể lấy danh sách hóa đơn');
        }
    }




    /**
     * Lấy chi tiết 1 hóa đơn của người dùng hiện tại
     */
    public function userInvoiceDetail($id = null)
    {
        $user = session()->get('user');

        if (!$user) {
            return $this->failUnauthorized('Người dùng chưa đăng nhập');
        }

        if (!$id) {
            return $this->failValidationErrors('Thiếu mã hóa đơn');
        }

        try {
            $invoice = $this->invoiceModel->getInvoiceByIdForCustomer($id, $user['user_id']);

            if (!$invoice) {
                return $this->failNotFound('Không tìm thấy hóa đơn hoặc không thuộc quyền sở hữu');
            }

            return $this->respond([
                'status'  => 'success',
                'data'    => $invoice,
                'message' => 'Chi tiết hóa đơn người dùng',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Lỗi khi lấy chi tiết hóa đơn khách hàng: ' . $e->getMessage());
            return $this->failServerError('Không thể lấy chi tiết hóa đơn');
        }
    }


}
