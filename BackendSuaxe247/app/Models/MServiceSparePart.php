<?php

namespace App\Models;

use CodeIgniter\Model;

class MServiceSparePart extends Model
{
    protected $table = 'service_sparepart';
    protected $primaryKey = 'PK_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'PK_id',
        'FK_idService', //ref invoice_service (PK_id)
        'FK_idSparePart',
        'quantity'
    ];
}
