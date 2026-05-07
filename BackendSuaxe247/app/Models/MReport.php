<?php

namespace App\Models;

use CodeIgniter\Model;

class MReport extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Báo cáo xuất nhập kho phụ tùng trong kỳ
     * @param string $from Ngày bắt đầu (YYYY-MM-DD)
     * @param string $to Ngày kết thúc (YYYY-MM-DD)
     * @return array
     */
    public function getWarehouseReport($from, $to, $storeId = null)
    {
        $user = session()->get('user');
        if (!$user) {
            return ['error' => 'Người dùng chưa đăng nhập'];
        }

        // Thông tin người dùng
        $roleName = $user['roleName'];
        $createdBy = $user['fullName'];

        // Nếu là Quản lý hệ thống hoặc Admin -> dùng storeId từ controller truyền xuống
        if (in_array($roleName, ['Quản lý hệ thống', 'Admin'])) {
            if (empty($storeId)) {
                return ['error' => 'Vui lòng chọn cửa hàng'];
            }
        } else {
            // Còn lại thì lấy theo session
            $storeId = $user['store'];
        }

        // Lấy thông tin cửa hàng
        $storeModel = new \App\Models\MStore();
        $store = $storeModel->where('PK_idStore', $storeId)->first();
        if (!$store) {
            return ['error' => 'Không tìm thấy cửa hàng'];
        }

        $storeAddress = $store['address'] ?? '';
        $storePhone   = $store['phone'] ?? '';

        // ====== 1. Lấy danh sách nhập trong kỳ ======
        $importData = $this->db->table('ImportReceiptDetail AS d')
            ->select('
                sp.PK_idSparePart,
                sp.sparePartName,
                sp.unit,
                sp.purchasePrice,
                SUM(d.importedQty) AS total_import_qty
            ')
            ->join('ImportReceipt AS r', 'r.PK_idImport = d.FK_idImport', 'left')
            ->join('SparePart AS sp', 'sp.PK_idSparePart = d.FK_idSparePart', 'left')
            ->where('r.FK_idStore', $storeId)
            ->where('r.deleted', 0)
            ->where('r.created >=', $from . ' 00:00:00')
            ->where('r.created <=', $to . ' 23:59:59')
            ->groupBy('sp.PK_idSparePart')
            ->get()
            ->getResultArray();

        // ====== 2. Lấy danh sách xuất trong kỳ ======
        $exportData = $this->db->table('service_sparepart AS ssp')
            ->select('
                sp.PK_idSparePart,
                sp.sparePartName,
                sp.unit,
                sp.salePrice,
                SUM(ssp.quantity) AS total_export_qty
            ')
            ->join('invoice_service AS isv', 'isv.PK_id = ssp.FK_idService', 'left')
            ->join('Invoice AS inv', 'inv.PK_idInvoice = isv.FK_idInvoice', 'left')
            ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
            ->where('inv.FK_idStore', $storeId)
            ->where('inv.deleted', 0)
            ->where('inv.created >=', $from . ' 00:00:00')
            ->where('inv.created <=', $to . ' 23:59:59')
            ->groupBy('sp.PK_idSparePart')
            ->get()
            ->getResultArray();

        // ====== 3. Gộp dữ liệu nhập + xuất ======
        $report = [];
        foreach ($importData as $imp) {
            $id = $imp['PK_idSparePart'];
            $report[$id] = [
                'sparePartId'   => $id,
                'sparePartName' => $imp['sparePartName'],
                'unit'          => $imp['unit'],
                'importQty'     => (float)$imp['total_import_qty'],
                'importPrice'   => (float)$imp['purchasePrice'],
                'importAmount'  => $imp['total_import_qty'] * $imp['purchasePrice'],
                'exportQty'     => 0,
                'exportPrice'   => 0,
                'exportAmount'  => 0,
            ];
        }

        foreach ($exportData as $exp) {
            $id = $exp['PK_idSparePart'];
            if (!isset($report[$id])) {
                $report[$id] = [
                    'sparePartId'   => $id,
                    'sparePartName' => $exp['sparePartName'],
                    'unit'          => $exp['unit'],
                    'importQty'     => 0,
                    'importPrice'   => 0,
                    'importAmount'  => 0,
                ];
            }
            $report[$id]['exportQty']   = (float)$exp['total_export_qty'];
            $report[$id]['exportPrice'] = (float)$exp['salePrice'];
            $report[$id]['exportAmount'] = $exp['total_export_qty'] * $exp['salePrice'];
        }

        // ====== 4. Tạo thông tin header ======
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $reportNo = 'XNK-' . strtoupper($storeId) . date('iHdmY');

        usort($report, fn($a, $b) => strcmp($a['sparePartId'], $b['sparePartId']));

        return [
            'storeAddress' => $storeAddress,
            'storePhone'   => $storePhone,
            'createdBy'    => $createdBy,
            'roleName'     => $roleName,
            'reportNo'     => $reportNo,
            'from'         => $from,
            'to'           => $to,
            'data'         => $report
        ];
    }



    public function getStoreReport($from, $to, $storeId = null)
{
    $user = session()->get('user');
    if (!$user) {
        return ['error' => 'Người dùng chưa đăng nhập'];
    }

    $roleName  = $user['roleName'];
    $createdBy = $user['fullName'];

    // ✅ Nếu là Quản lý hệ thống hoặc Admin thì dùng storeId truyền từ controller
    if (in_array($roleName, ['Quản lý hệ thống', 'Admin'])) {
        if (empty($storeId)) {
            return ['error' => 'Vui lòng chọn cửa hàng'];
        }
    } else {
        // ✅ Nếu là Quản lý cửa hàng thì dùng store trong session
        $storeId = $user['store'];
    }

    // ✅ Lấy thông tin cửa hàng từ model
    $storeModel = new \App\Models\MStore();
    $store = $storeModel->where('PK_idStore', $storeId)->first();
    if (!$store) {
        return ['error' => 'Không tìm thấy cửa hàng'];
    }

    $storeAddr = $store['address'] ?? '';
    $storePhone = $store['phone'] ?? '';

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $reportNo = 'HDCH-' . strtoupper($storeId) . date('iHdmY');

    $db = $this->db;

    // ===== 1. Tổng doanh thu =====
    $invoices = $db->table('Invoice')
        ->select('PK_idInvoice')
        ->where('FK_idStore', $storeId)
        ->where('deleted', 0)
        ->where('created >=', $from . ' 00:00:00')
        ->where('created <=', $to . ' 23:59:59')
        ->get()
        ->getResultArray();

    $totalRevenue = 0;
    $totalLaborRevenue = 0;
    $totalPartRevenue  = 0;

    foreach ($invoices as &$inv) {
        $partRow = $db->table('invoice_service AS isv')
            ->select('SUM(sp.salePrice * ssp.quantity) AS totalPart')
            ->join('service_sparepart AS ssp', 'ssp.FK_idService = isv.PK_id', 'left')
            ->join('SparePart AS sp', 'sp.PK_idSparePart = ssp.FK_idSparePart', 'left')
            ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
            ->get()
            ->getRowArray();

        $laborRow = $db->table('invoice_service AS isv')
            ->select('SUM(isv.laborCost) AS totalLabor')
            ->where('isv.FK_idInvoice', $inv['PK_idInvoice'])
            ->get()
            ->getRowArray();

        $part = (float)($partRow['totalPart'] ?? 0);
        $labor = (float)($laborRow['totalLabor'] ?? 0);
        $inv['totalAmount'] = $part + $labor;

        $totalPartRevenue  += $part;
        $totalLaborRevenue += $labor;
        $totalRevenue      += $inv['totalAmount'];
    }

    // ===== 2. Tổng số lượng khách =====
    $invoiceCountData = $db->table('Invoice')
        ->select('COUNT(PK_idInvoice) as totalInvoiceCount')
        ->where('FK_idStore', $storeId)
        ->where('deleted', 0)
        ->where('created >=', $from . ' 00:00:00')
        ->where('created <=', $to . ' 23:59:59')
        ->get()
        ->getRowArray();

    $totalCustomerVisits = (int)($invoiceCountData['totalInvoiceCount'] ?? 0);

    // 2.1 Khách cũ
    $oldCustomers = $db->table('Invoice as i1')
        ->select('COUNT(DISTINCT i1.FK_idCustomer) as oldCustomerCount')
        ->join('Invoice as i2', 'i1.FK_idCustomer = i2.FK_idCustomer', 'inner')
        ->where('i1.FK_idStore', $storeId)
        ->where('i1.deleted', 0)
        ->where('i2.FK_idStore', $storeId)
        ->where('i2.deleted', 0)
        ->where('i1.created <', $from . ' 00:00:00')
        ->where('i2.created >=', $from . ' 00:00:00')
        ->where('i2.created <=', $to . ' 23:59:59')
        ->get()->getRowArray();
    $oldCustomerCount = (int)($oldCustomers['oldCustomerCount'] ?? 0);

    // 2.2 Khách mới
    $newCustomers = $db->table('Invoice as i1')
        ->select('COUNT(DISTINCT i1.FK_idCustomer) as newCustomerCount')
        ->where('i1.FK_idStore', $storeId)
        ->where('i1.deleted', 0)
        ->where('i1.created >=', $from . ' 00:00:00')
        ->where('i1.created <=', $to . ' 23:59:59')
        ->whereNotIn('i1.FK_idCustomer', function($builder) use($storeId, $from){
            $builder->select('FK_idCustomer')
                ->from('Invoice')
                ->where('FK_idStore', $storeId)
                ->where('deleted', 0)
                ->where('created <', $from . ' 00:00:00');
        })
        ->get()->getRowArray();
    $newCustomerCount = (int)($newCustomers['newCustomerCount'] ?? 0);

    // ===== 3. Tổng số lượng đặt lịch =====
    $appointmentData = $db->table('Appointment')
        ->select('COUNT(*) as totalAppointment')
        ->where('FK_idStore', $storeId)
        ->where('deleted', 0)
        ->where('appointmentDate >=', $from)
        ->where('appointmentDate <=', $to)
        ->get()->getRowArray();
    $totalAppointment = (int)($appointmentData['totalAppointment'] ?? 0);

    // 3.1 Số lượng hoàn thành
    $completedData = $db->table('Appointment')
        ->select('COUNT(*) as completedCount')
        ->where('FK_idStore', $storeId)
        ->where('deleted', 0)
        ->where('appointmentDate >=', $from)
        ->where('appointmentDate <=', $to)
        ->where('status', 'Hoàn thành')
        ->get()->getRowArray();
    $completedCount = (int)($completedData['completedCount'] ?? 0);

    $completionRate = $totalAppointment > 0 ? round($completedCount / $totalAppointment * 100, 2) : 0;

    // ===== 4. Tổng số lượng dịch vụ đã sử dụng =====
    $serviceData = $db->table('invoice_service as isv')
        ->select('s.serviceName, COUNT(*) as serviceCount')
        ->join('Service as s', 's.PK_idService = isv.FK_idService', 'left')
        ->join('Invoice as inv', 'inv.PK_idInvoice = isv.FK_idInvoice', 'left')
        ->where('inv.FK_idStore', $storeId)
        ->where('inv.deleted', 0)
        ->where('inv.created >=', $from . ' 00:00:00')
        ->where('inv.created <=', $to . ' 23:59:59')
        ->groupBy('s.serviceName')
        ->orderBy('serviceCount', 'DESC')
        ->get()
        ->getResultArray();

    $totalServiceUsed = array_sum(array_column($serviceData, 'serviceCount'));

    // ===== 5. Trả kết quả =====
    return [
        'storeAddress'         => $storeAddr,
        'storePhone'           => $storePhone,
        'createdBy'            => $createdBy,
        'roleName'             => $roleName,
        'reportNo'             => $reportNo,
        'from'                 => $from,
        'to'                   => $to,
        'totalRevenue'         => $totalRevenue,
        'totalLaborRevenue'    => $totalLaborRevenue,
        'totalPartRevenue'     => $totalPartRevenue,
        'totalCustomerVisits'  => $totalCustomerVisits,
        'customerOldCount'     => $oldCustomerCount,
        'customerNewCount'     => $newCustomerCount,
        'totalAppointment'     => $totalAppointment,
        'completedAppointment' => $completedCount,
        'completionRate'       => $completionRate,
        'totalServiceUsed'     => $totalServiceUsed,
        'serviceDetails'       => $serviceData,
    ];
}




}
