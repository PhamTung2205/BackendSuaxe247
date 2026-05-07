<?php

namespace App\Models;

use CodeIgniter\Model;

class MInvoice extends Model
{
    protected $table = 'Invoice';
    protected $primaryKey = 'PK_idInvoice';
    protected $allowedFields = [
        'PK_idInvoice',
        'FK_idAppointment',
        'FK_idVehicle',
        'FK_idCustomer',
        'FK_idStore',
        'FK_idCashier',
        'FK_idTechnician',
        'checkInTime',
        'checkOutTime',
        'customerName',
        'kmNumber',
        'customerRequest',
        'postRepairStatus',
        'paymentMethod',
        'status',
        'created',
        'deleted'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created';
    protected $updatedField  = false; 
    protected $deletedField  = 'deleted';

    /**
     * Lấy danh sách hóa đơn + tổng tiền (phụ tùng + công)
     */
    public function getAllInvoice($filters = [], $userSession = null)
    {
        $builder = $this->db->table($this->table)
            ->select('
                Invoice.*,
                customer.fullName AS customer_name,
                cashier.fullName AS cashier_name,
                technician.fullName AS technician_name,
                Vehicle.licensePlate AS vehicle_license,
                Vehicle.type AS vehicle_type,
                Store.address AS store_address
            ')
            ->join('User AS customer', 'customer.PK_idUser = Invoice.FK_idCustomer', 'left')
            ->join('User AS cashier', 'cashier.PK_idUser = Invoice.FK_idCashier', 'left')
            ->join('User AS technician', 'technician.PK_idUser = Invoice.FK_idTechnician', 'left')
            ->join('Vehicle', 'Vehicle.PK_idVehicle = Invoice.FK_idVehicle', 'left')
            ->join('Store', 'Store.PK_idStore = Invoice.FK_idStore', 'left')
            ->where('Invoice.deleted', 0)
            ->orderBy('Invoice.created', 'DESC');

        // Lọc theo role
        if ($userSession) {
            $role = $userSession['roleName'] ?? null;
            $storeId = $userSession['store'] ?? null;
            $userId = $userSession['user_id'] ?? null;

            if ($role === 'Quản lý cửa hàng') {
                $builder->where('Invoice.FK_idStore', $storeId);
            } elseif ($role === 'Kỹ thuật viên') {
                $builder->where('Invoice.FK_idTechnician', $userId);
            } elseif (in_array($role, ['Quản lý hệ thống', 'Admin']) && !empty($filters['store_id'])) {
                $builder->where('Invoice.FK_idStore', $filters['store_id']);
            }
        }

        // Filter tìm kiếm
        if (!empty($filters['invoice_id'])) {
            $builder->like('Invoice.PK_idInvoice', $filters['invoice_id']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('Invoice.customerName', $filters['search'])
                ->orLike('technician.fullName', $filters['search'])
                ->orLike('cashier.fullName', $filters['search'])
                ->groupEnd();
        }

        // Lọc theo ngày tạo
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            // Nếu có cả 2 mốc thời gian
            $builder->where('DATE(Invoice.created) >=', $filters['date_from']);
            $builder->where('DATE(Invoice.created) <=', $filters['date_to']);
        } elseif (!empty($filters['date_from'])) {
            // Nếu chỉ có date_from thì tìm từ ngày đó đến hiện tại
            $builder->where('DATE(Invoice.created) >=', $filters['date_from']);
            $builder->where('DATE(Invoice.created) <=', date('Y-m-d'));
        } elseif (!empty($filters['date_to'])) {
            // Nếu chỉ có date_to (hiếm khi xảy ra) thì lấy đến ngày đó
            $builder->where('DATE(Invoice.created) <=', $filters['date_to']);
        }


        $invoices = $builder->get()->getResultArray();

        // Tính tổng tiền: (phụ tùng * số lượng + công)
        foreach ($invoices as &$inv) {
            // Tổng tiền phụ tùng
            $partRow = $this->db->table('invoice_service AS isv')
                ->select('SUM(sp.salePrice * ssp.quantity) AS totalPart')
                ->join('service_sparepart AS ssp', 'ssp.FK_idService = isv.PK_id', 'left')
                ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
                ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
                ->get()
                ->getRowArray();

            // Tổng tiền công (chỉ tính mỗi dịch vụ 1 lần)
            $laborRow = $this->db->table('invoice_service AS isv')
                ->select('SUM(isv.laborCost) AS totalLabor')
                ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
                ->get()
                ->getRowArray();

            $part = (float)($partRow['totalPart'] ?? 0);
            $labor = (float)($laborRow['totalLabor'] ?? 0);
            $inv['totalAmount'] = $part + $labor;
        }


        return $invoices;
    }

    /**
     * Lấy chi tiết hóa đơn: gồm dịch vụ + phụ tùng của từng dịch vụ
     */
    public function getInvoiceById($id)
    {
        // Thông tin hóa đơn
        $invoice = $this->db->table('Invoice')
            ->select('
                Invoice.*,
                customer.fullName AS customer_name,
                customer.phone,
                cashier.fullName AS cashier_name,
                technician.fullName AS technician_name,
                Vehicle.licensePlate AS vehicle_license,
                Vehicle.type AS vehicle_type,
                Store.address AS store_address, 
                Store.phone AS store_phone,
            ')
            ->join('User AS customer', 'customer.PK_idUser = Invoice.FK_idCustomer', 'left')
            ->join('User AS cashier', 'cashier.PK_idUser = Invoice.FK_idCashier', 'left')
            ->join('User AS technician', 'technician.PK_idUser = Invoice.FK_idTechnician', 'left')
            ->join('Vehicle', 'Vehicle.PK_idVehicle = Invoice.FK_idVehicle', 'left')
            ->join('Store', 'Store.PK_idStore = Invoice.FK_idStore', 'left')
            ->where('Invoice.PK_idInvoice', $id)
            ->where('Invoice.deleted', 0)
            ->get()
            ->getRowArray();

        if (!$invoice) return null;

        // Lấy danh sách dịch vụ và phụ tùng của mỗi dịch vụ
        $services = $this->db->table('invoice_service AS isv')
            ->select('isv.*, s.serviceName')
            ->join('Service AS s', 's.PK_idService = isv.FK_idService', 'left')
            ->where('isv.FK_idInvoice', $id)
            ->get()
            ->getResultArray();

        $total = 0;

        foreach ($services as &$sv) {
            $parts = $this->db->table('service_sparepart AS ssp')
                ->select('ssp.*, sp.sparePartName, sp.salePrice, sp.unit')
                ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
                ->where('ssp.FK_idService', $sv['PK_id'])
                ->get()
                ->getResultArray();

            $sv['spareParts'] = $parts;

            // Tính tổng cho dịch vụ
            $partTotal = 0;
            foreach ($parts as $p) {
                $partTotal += ($p['salePrice'] ?? 0) * ($p['quantity'] ?? 0);
            }

            $sv['total'] = $partTotal + (float)($sv['laborCost'] ?? 0);
            $total += $sv['total'];
        }

        $invoice['services'] = $services;
        $invoice['totalAmount'] = $total;

        return $invoice;
    }

    public function deleteInvoice($id)
    {
        return $this->db->table($this->table)
            ->where('PK_idInvoice', $id)
            ->update(['deleted' => 1]);
    }

    // Sinh PK_idInvoice theo cửa hàng
    private function generateInvoiceCode($storeId)
    {
        // Lấy phần số lớn nhất của mã hiện có
        $query = $this->db->query("
            SELECT 
                CAST(SUBSTRING(PK_idInvoice, LENGTH(?) + 2) AS UNSIGNED) AS num
            FROM {$this->table}
            WHERE PK_idInvoice LIKE CONCAT(?, '-%')
            ORDER BY num DESC
            LIMIT 1
        ", [$storeId, $storeId]);

        $row = $query->getRowArray();
        $newNumber = $row ? ((int)$row['num'] + 1) : 1;

        // Format 6 chữ số: 000001, 000002, ...
        return $storeId . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }



    public function addInvoice(array $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $MUser = new \App\Models\MUser();
        $MVehicle = new \App\Models\MVehicle();

        try {
            // 1️⃣ Kiểm tra hoặc thêm khách hàng
            $customer = $MUser->getUserByPhone($data['phone']);
            if (!$customer) {
                $customerId = $MUser->insert([
                    'fullName' => $data['customerName'],
                    'phone'    => $data['phone'],
                    'FK_idRole'=> 4,
                    'deleted'  => 0
                ], true);

                if (!$customerId) {
                    $db->transRollback();
                    return ['status' => false, 'message' => 'Tạo khách hàng thất bại'];
                }
            } else {
                $customerId = (int)$customer['PK_idUser'];
            }

            // 2️⃣ Kiểm tra hoặc thêm xe
            $vehicle = $db->table('Vehicle')
                ->where('licensePlate', $data['vehicle_license'])
                ->where('FK_idUser', $customerId)
                ->where('deleted', 0)
                ->get()
                ->getRowArray();

            if (!$vehicle) {
                $vehicleId = $MVehicle->insert([
                    'FK_idUser'    => $customerId,
                    'licensePlate' => $data['vehicle_license'],
                    'type'         => $data['vehicle_type'],
                    'deleted'      => 0
                ], true);
            } else {
                $vehicleId = (int)$vehicle['PK_idVehicle'];
            }

            // 3️⃣ Thêm hóa đơn
            $invoiceCode = $this->generateInvoiceCode($data['FK_idStore'] ?? '');
            $invoiceData = [
                'PK_idInvoice'    => $invoiceCode,
                'FK_idCustomer'   => $customerId,
                'FK_idVehicle'    => $vehicleId,
                'FK_idStore'      => $data['FK_idStore'] ?? null,
                'FK_idCashier'    => $data['FK_idCashier'] ?? null,
                'FK_idTechnician' => $data['FK_idTechnician'] ?? null,
                'FK_idAppointment'=> $data['FK_idAppointment'] ?? null,
                'checkInTime'     => date('Y-m-d H:i:s', strtotime($data['checkInTime'])),
                'checkOutTime'    => date('Y-m-d H:i:s', strtotime($data['checkOutTime'])),
                'customerName'    => $data['customerName'],
                'kmNumber'        => $data['kmNumber'] ?? 0,
                'customerRequest' => $data['customerRequest'] ?? '',
                'postRepairStatus'=> $data['postRepairStatus'] ?? '',
                'paymentMethod'   => $data['paymentMethod'] ?? null,
                'status'          => $data['status'] ?? 'Đã thanh toán',
                'deleted'         => 0,
                'created'         => date('Y-m-d H:i:s')
            ];

            $db->table('Invoice')->insert($invoiceData);
            $invoiceId = $invoiceCode;

            // 3.5️⃣ Nếu có lịch hẹn → cập nhật trạng thái "Hoàn thành"
            if (!empty($data['FK_idAppointment'])) {
                $db->table('appointment')
                    ->where('PK_idAppointment', $data['FK_idAppointment'])
                    ->update([
                        'status'  => 'Hoàn thành',
                        'updated' => date('Y-m-d H:i:s')
                    ]);
            }

            // 4️⃣ Thêm dịch vụ và phụ tùng
            if (!empty($data['services']) && is_array($data['services'])) {
                foreach ($data['services'] as $service) {
                    $serviceData = [
                        'FK_idInvoice' => $invoiceId,
                        'FK_idService' => $service['serviceId'],
                        'laborCost'    => $service['laborCost'] ?? 0,
                    ];
                    $db->table('invoice_service')->insert($serviceData);
                    $invoiceServiceId = $db->insertID();

                    if (!empty($service['spareParts']) && is_array($service['spareParts'])) {
                        foreach ($service['spareParts'] as $sp) {
                            $storeSp = $db->table('Store_SparePart')
                                ->where('FK_idStore', $data['FK_idStore'])
                                ->where('FK_idSparePart', $sp['sparePartId'])
                                ->where('deleted', 0)
                                ->get()
                                ->getRowArray();

                            if (!$storeSp) {
                                $db->transRollback();
                                return [
                                    'status' => false,
                                    'message' => "Phụ tùng {$sp['sparePartId']} không có trong kho cửa hàng"
                                ];
                            }

                            $newQty = $storeSp['stockQty'] - ($sp['quantity'] ?? 1);
                            if ($newQty < 0) {
                                $db->transRollback();
                                return [
                                    'status' => false,
                                    'message' => "Số lượng phụ tùng {$sp['sparePartId']} không đủ trong kho. Tồn kho hiện tại: {$storeSp['stockQty']}"
                                ];
                            }

                            $db->table('Store_SparePart')
                                ->where('PK_idSSP', $storeSp['PK_idSSP'])
                                ->update([
                                    'stockQty' => $newQty,
                                    'updated'  => date('Y-m-d H:i:s'),
                                ]);

                            $db->table('service_sparepart')->insert([
                                'FK_idService'   => $invoiceServiceId,
                                'FK_idSparePart' => $sp['sparePartId'],
                                'quantity'       => $sp['quantity'] ?? 1,
                            ]);
                        }
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['status' => false, 'message' => 'Tạo hóa đơn thất bại'];
            }

            return ['status' => true, 'message' => 'Tạo hóa đơn thành công', 'invoiceId' => $invoiceCode];

        } catch (\Exception $e) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Lỗi server: ' . $e->getMessage()];
        }
    }


    /**
     * Lấy danh sách hóa đơn (lịch sử sửa chữa) của người dùng hiện tại (theo session)
     */
    public function getInvoicesByCurrentUser($filters = [], $page = 1, $perPage = 6)
    {
        $sessionUser = session()->get('user');
        if (!$sessionUser) {
            return [];
        }

        $userId = $sessionUser['user_id'] ?? null;
        if (!$userId) {
            return [];
        }

        // --- Chuẩn bị query cơ bản ---
        $builder = $this->db->table('Invoice')
            ->select('
                Invoice.*,
                Vehicle.licensePlate AS vehicle_license,
                Vehicle.type AS vehicle_type,
                Store.address AS store_address
            ')
            ->join('Vehicle', 'Vehicle.PK_idVehicle = Invoice.FK_idVehicle', 'left')
            ->join('Store', 'Store.PK_idStore = Invoice.FK_idStore', 'left')
            ->where('Invoice.FK_idCustomer', $userId)
            ->where('Invoice.deleted', 0);

        // --- Lọc theo từ khóa ---
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('Invoice.PK_idInvoice', $filters['search'])
                ->orLike('Vehicle.licensePlate', $filters['search'])
                ->groupEnd();
        }

        // --- Lọc theo ngày ---
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            // Có cả hai ngày: lọc trong khoảng
            $builder->where('DATE(Invoice.created) >=', $filters['date_from']);
            $builder->where('DATE(Invoice.created) <=', $filters['date_to']);
        } elseif (!empty($filters['date_from'])) {
            // Chỉ có date_from: tìm từ ngày đó đến hiện tại
            $builder->where('DATE(Invoice.created) >=', $filters['date_from']);
            $builder->where('DATE(Invoice.created) <=', date('Y-m-d'));
        } elseif (!empty($filters['date_to'])) {
            // Chỉ có date_to: tìm đến ngày đó
            $builder->where('DATE(Invoice.created) <=', $filters['date_to']);
        }


        // --- Lấy tất cả hóa đơn sau các filter cơ bản ---
        $builder->orderBy('Invoice.created', 'DESC');
        $invoices = $builder->get()->getResultArray();

        // --- Tính tổng chi phí cho mỗi hóa đơn ---
        foreach ($invoices as &$inv) {
            $partRow = $this->db->table('invoice_service AS isv')
                ->select('SUM(sp.salePrice * ssp.quantity) AS totalPart')
                ->join('service_sparepart AS ssp', 'ssp.FK_idService = isv.PK_id', 'left')
                ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
                ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
                ->get()
                ->getRowArray();

            $laborRow = $this->db->table('invoice_service AS isv')
                ->select('SUM(isv.laborCost) AS totalLabor')
                ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
                ->get()
                ->getRowArray();

            $part = (float)($partRow['totalPart'] ?? 0);
            $labor = (float)($laborRow['totalLabor'] ?? 0);
            $inv['totalAmount'] = $part + $labor;
        }

        // --- Lọc theo khoảng chi phí ---
        if (!empty($filters['cost_range'])) {
            $range = $filters['cost_range'];
            $invoices = array_filter($invoices, function ($inv) use ($range) {
                $total = $inv['totalAmount'] ?? 0;
                switch ($range) {
                    case '100000':
                        return $total < 100000;
                    case '300000':
                        return $total >= 100000 && $total < 300000;
                    case '500000':
                        return $total >= 300000 && $total < 500000;
                    case 'above500000':
                        return $total >= 500000;
                    default:
                        return true;
                }
            });
            $invoices = array_values($invoices);
        }

        // --- Tính lại tổng sau khi lọc ---
        $totalCount = count($invoices);

        // --- Phân trang ---
        $totalPages = max(1, ceil($totalCount / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        // --- Cắt dữ liệu cho trang hiện tại ---
        $pagedInvoices = array_slice($invoices, $offset, $perPage);

        return [
            'data' => $pagedInvoices,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $totalCount,
                'total_pages' => $totalPages,
            ]
        ];
    }



    /**
     * Lấy chi tiết hóa đơn dành cho khách hàng (chỉ cho phép xem hóa đơn của chính mình)
     */
    public function getInvoiceByIdForCustomer($invoiceId, $customerId = null)
    {
        // Lấy ID khách hàng hiện tại nếu chưa truyền vào
        if (!$customerId) {
            $user = session()->get('user');
            $customerId = $user['user_id'] ?? null;
        }

        if (!$customerId) {
            return null;
        }

        // Truy vấn thông tin hóa đơn (giống getInvoiceById)
        $invoice = $this->db->table('Invoice')
            ->select('
                Invoice.*,
                customer.fullName AS customerName,
                customer.phone,
                cashier.fullName AS cashier_name,
                technician.fullName AS technician_name,
                Vehicle.licensePlate AS vehicle_license,
                Vehicle.type AS vehicle_type,
                Store.address AS store_address, 
                Store.phone AS store_phone
            ')
            ->join('User AS customer', 'customer.PK_idUser = Invoice.FK_idCustomer', 'left')
            ->join('User AS cashier', 'cashier.PK_idUser = Invoice.FK_idCashier', 'left')
            ->join('User AS technician', 'technician.PK_idUser = Invoice.FK_idTechnician', 'left')
            ->join('Vehicle', 'Vehicle.PK_idVehicle = Invoice.FK_idVehicle', 'left')
            ->join('Store', 'Store.PK_idStore = Invoice.FK_idStore', 'left')
            ->where('Invoice.PK_idInvoice', $invoiceId)
            ->where('Invoice.FK_idCustomer', $customerId) // 🔒 đảm bảo chỉ xem hóa đơn của mình
            ->where('Invoice.deleted', 0)
            ->get()
            ->getRowArray();

        if (!$invoice) {
            return null;
        }

        // Lấy danh sách dịch vụ trong hóa đơn
        $services = $this->db->table('invoice_service AS isv')
            ->select('isv.*, s.serviceName')
            ->join('Service AS s', 's.PK_idService = isv.FK_idService', 'left')
            ->where('isv.FK_idInvoice', $invoiceId)
            ->get()
            ->getResultArray();

        $total = 0;

        // Gắn danh sách phụ tùng cho từng dịch vụ
        foreach ($services as &$sv) {
            $parts = $this->db->table('service_sparepart AS ssp')
                ->select('ssp.*, sp.sparePartName, sp.salePrice, sp.unit')
                ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
                ->where('ssp.FK_idService', $sv['PK_id'])
                ->get()
                ->getResultArray();

            $sv['spareParts'] = $parts;

            // Tính tổng tiền của từng dịch vụ
            $partTotal = 0;
            foreach ($parts as $p) {
                $partTotal += ($p['salePrice'] ?? 0) * ($p['quantity'] ?? 0);
            }

            $sv['total'] = $partTotal + (float)($sv['laborCost'] ?? 0);
            $total += $sv['total'];
        }

        // Gắn kết quả vào hóa đơn
        $invoice['services'] = $services;
        $invoice['totalAmount'] = $total;

        return $invoice;
    }



}
