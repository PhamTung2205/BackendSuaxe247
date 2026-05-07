<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MImportReceipt extends Model
{
    protected $table = 'importreceipt';
    protected $primaryKey = 'PK_idImport';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['PK_idImport', 'FK_idStore', 'FK_idSupplier', 'FK_idCreatedBy', 'deliveryReceipt'];
    protected $useTimestamps = true;
    protected $createdField = 'created';
    protected $updatedField = 'updated';
    protected $useSoftDeletes = false;
    protected $deletedField = 'deleted';

    public function getFilteredImportReceipts(array $params)
    {
        $builder = $this->db->table($this->table . ' AS ir');
        
        $builder->select('
            ir.PK_idImport, 
            ir.FK_idStore, 
            ir.created,
            ir.deliveryReceipt,
            s.address AS storeAddress,
            sup.supplierName,
            u.fullName AS createdByFullName
        ');
        $builder->join('store s', 's.PK_idStore = ir.FK_idStore', 'left');
        $builder->join('supplier sup', 'sup.PK_idSupplier = ir.FK_idSupplier', 'left');
        $builder->join('user u', 'u.PK_idUser = ir.FK_idCreatedBy', 'left');
        $builder->where('ir.deleted', 0);

        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('ir.PK_idImport', $params['search'])
                ->orLike('sup.supplierName', $params['search'])
                ->orLike('s.address', $params['search'])
                ->orLike('ir.deliveryReceipt', $params['search'])
                ->groupEnd();
        }
        
        if (!empty($params['storeId'])) {
            $builder->where('ir.FK_idStore', $params['storeId']);
        }
        
        if (!empty($params['supplierId'])) {
            $builder->where('ir.FK_idSupplier', $params['supplierId']);
        }
        
        if (!empty($params['startDate'])) {
            $builder->where('DATE(ir.created) >=', $params['startDate']);
        }
        
        if (!empty($params['endDate'])) {
            $builder->where('DATE(ir.created) <=', $params['endDate']);
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $offset = ($params['page'] - 1) * $params['limit'];
        $builder->orderBy('ir.created', 'DESC');
        $builder->limit($params['limit'], $offset);
        $data = $builder->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    public function getReceiptDetails(string $receiptId)
    {
        $mainInfo = $this->db->table($this->table . ' AS ir')
            ->select('ir.*, s.address as storeAddress, sup.supplierName, u.fullName as createdByFullName')
            ->join('store s', 's.PK_idStore = ir.FK_idStore', 'left')
            ->join('supplier sup', 'sup.PK_idSupplier = ir.FK_idSupplier', 'left')
            ->join('user u', 'u.PK_idUser = ir.FK_idCreatedBy', 'left')
            ->where('ir.PK_idImport', $receiptId)
            ->where('ir.deleted', 0)
            ->get()->getRowArray();

        if (!$mainInfo) {
            return null;
        }

        $details = $this->db->table('importreceiptdetail ird')
            ->select('ird.importedQty, ird.requestedQty, sp.purchasePrice AS importPrice, sp.PK_idSparePart, sp.sparePartName, sp.unit')
            ->join('sparepart sp', 'sp.PK_idSparePart = ird.FK_idSparePart', 'left')
            ->where('ird.FK_idImport', $receiptId)
            ->get()->getResultArray();

        return ['main' => $mainInfo, 'details' => $details];
    }

    public function createImportReceipt(array $mainData, array $detailsData)
    {
        $this->db->transStart();

        $newReceiptId = 'PN' . date('YmdHis');
        $mainData['PK_idImport'] = $newReceiptId;
        $this->insert($mainData);

        $batchData = [];
        foreach ($detailsData as $item) {
            $batchData[] = [
                'FK_idImport'    => $newReceiptId,
                'FK_idSparePart' => $item['sparePartId'],
                'importedQty'    => $item['importedQty'],
                'requestedQty'   => $item['requestedQty'],
            ];
        }

        if (!empty($batchData)) {
            $this->db->table('importreceiptdetail')->insertBatch($batchData);
        }
        
        $storeId = $mainData['FK_idStore'];
        foreach ($detailsData as $item) {
            $sparePartId = $item['sparePartId'];
            $importedQty = (int)$item['importedQty'];

            $this->db->table('store_sparepart')
                     ->set('stockQty', "stockQty + $importedQty", false)
                     ->where('FK_idStore', $storeId)
                     ->where('FK_idSparePart', $sparePartId)
                     ->update();
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new Exception('Không thể tạo phiếu nhập kho do lỗi cập nhật tồn kho.');
        }

        return $newReceiptId;
    }

     public function manualSoftDeleteWithDetails(string $receiptId): bool
    {
        $this->db->transStart();

        $this->db->table($this->table)
                 ->where($this->primaryKey, $receiptId)
                 ->set('deleted', 1)
                 ->set('updated', date('Y-m-d H:i:s')) 
                 ->update();

        $this->db->table('importreceiptdetail')
                 ->where('FK_idImport', $receiptId)
                 ->set('deleted', 1)
                 ->update();
        
        $this->db->transComplete();

        return $this->db->transStatus();
    }
}