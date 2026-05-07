<?php

namespace App\Models;

use CodeIgniter\Model;

class MInvoiceService extends Model
{
    protected $table = 'invoice_service';
    protected $primaryKey = 'PK_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'PK_id',
        'FK_idInvoice',
        'FK_idService',
        'laborCost'
    ];

    public function getByInvoiceId($invoiceId)
    {
        return $this->db->table($this->table)
            ->select('InvoiceDetail.*, SparePart.sparepartName AS sparepart_name')
            ->join('SparePart', 'SparePart.PK_idSparePart = InvoiceDetail.FK_idSparePart', 'left')
            ->where('InvoiceDetail.FK_idInvoice', $invoiceId)
            ->get()
            ->getResultArray();
    }
}
