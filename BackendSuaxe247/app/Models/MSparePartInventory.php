<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MSparePartInventory extends Model
{
    protected $table = 'sparepart';
    protected $primaryKey = 'PK_idSparePart';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'PK_idSparePart', 'FK_idCategory', 'sparePartName',
        'unit', 'purchasePrice', 'salePrice', 'description'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created';
    protected $updatedField = 'updated';

    protected $validationRules = [
        'sparePartName'  => 'permit_empty|min_length[2]|max_length[255]',
        'unit'           => 'permit_empty|min_length[1]|max_length[50]',
        'purchasePrice'  => 'permit_empty|regex_match[/^[0-9]+$/]|greater_than[0]',
        'salePrice'      => 'permit_empty|regex_match[/^[0-9]+$/]|greater_than[0]',
        'FK_idCategory'  => 'permit_empty|alpha_numeric_punct|min_length[2]|max_length[20]'
    ];

    protected $validationMessages = [
        'purchasePrice' => [
            'regex_match' => 'Giá mua phải là số nguyên dương hợp lệ (không chứa ký tự đặc biệt).',
            'greater_than' => 'Giá mua phải lớn hơn 0.',
        ],
        'salePrice' => [
            'regex_match' => 'Giá bán phải là số nguyên dương hợp lệ (không chứa ký tự đặc biệt).',
            'greater_than' => 'Giá bán phải lớn hơn 0.',
        ],
    ];

    // public function getFilteredSpareParts(
    //     int $userId,
    //     ?string $search = null,
    //     ?string $categoryId = null,
    //     ?string $storeId = null,
    //     int $limit = 10,
    //     int $offset = 0
    // ): array {
    //     $user = $this->db->table('user u')
    //         ->join('role r', 'u.FK_idRole = r.PK_idRole', 'left')
    //         ->where('u.PK_idUser', $userId)
    //         ->select('r.roleName, u.FK_idStore')
    //         ->get()
    //         ->getRow();

    //     if (!$user) {
    //         return ['data' => [], 'total' => 0];
    //     }

    //     $builder = $this->db->table('store_sparepart ssp');
    //     $builder->select('
    //         ssp.FK_idStore,
    //         ssp.stockQty,
    //         ssp.warningQty,
    //         ssp.location,
    //         sp.PK_idSparePart,
    //         sp.sparePartName,
    //         sp.unit,
    //         sp.purchasePrice,
    //         sp.salePrice,
    //         sp.description,        
    //         sp.FK_idCategory,
    //         s.address AS storeAddress
    //     ');
    //     $builder->join('sparepart sp', 'ssp.FK_idSparePart = sp.PK_idSparePart');
    //     $builder->join('store s', 'ssp.FK_idStore = s.PK_idStore');
    //     $builder->where('sp.deleted', 0);
    //     $builder->where('ssp.deleted', 0);

    //     $canViewAll = in_array($user->roleName, [
    //         "Admin", "Quản lý hệ thống", "Giám đốc", "Kế toán", "Bộ phận kho"
    //     ]);
    //     if (!$canViewAll) {
    //         $builder->where('ssp.FK_idStore', $user->FK_idStore);
    //     }

    //     if (!empty($storeId)) {
    //         $builder->where('ssp.FK_idStore', $storeId);
    //     }
    //     if (!empty($categoryId) && $categoryId !== 'all') {
    //         $builder->where('sp.FK_idCategory', $categoryId);
    //     }
    //     if (!empty($search)) {
    //         $builder->like('sp.sparePartName', $search);
    //     }

    //     $total = $builder->countAllResults(false);

    //     $builder->orderBy('sp.created', 'DESC');
    //     $builder->limit($limit, $offset);
    //     $data = $builder->get()->getResultArray();

    //     return [
    //         'data' => $data,
    //         'total' => $total
    //     ];
    // }

    public function getFilteredSpareParts(
        int $userId,
        ?string $search = null,
        ?string $categoryId = null,
        ?string $storeId = null,
        int $limit = 10,
        int $offset = 0
    ): array {
        $user = $this->db->table('user u')
            ->join('role r', 'u.FK_idRole = r.PK_idRole', 'left')
            ->where('u.PK_idUser', $userId)
            ->select('r.roleName, u.FK_idStore')
            ->get()
            ->getRow();

        if (!$user) {
            return ['data' => [], 'total' => 0];
        }

        $builder = $this->db->table('store_sparepart ssp');
        $builder->join('sparepart sp', 'ssp.FK_idSparePart = sp.PK_idSparePart');
        $builder->join('store s', 'ssp.FK_idStore = s.PK_idStore');
        $builder->where('sp.deleted', 0);
        $builder->where('ssp.deleted', 0);

        $canViewAll = in_array($user->roleName, [
            "Admin", "Quản lý hệ thống", "Giám đốc", "Kế toán", "Bộ phận kho"
        ]);
        if (!$canViewAll) {
            $builder->where('ssp.FK_idStore', $user->FK_idStore);
        }

        if (!empty($storeId)) {
            $builder->where('ssp.FK_idStore', $storeId);
        }
        if (!empty($categoryId) && $categoryId !== 'all') {
            $builder->where('sp.FK_idCategory', $categoryId);
        }
        if (!empty($search)) {
            $builder->like('sp.sparePartName', $search);
        }

        $total = $builder->countAllResults(false); 

        $builder->select("
            ssp.FK_idStore,
            ssp.stockQty,
            ssp.warningQty,
            ssp.location,
            sp.PK_idSparePart,
            sp.sparePartName,
            sp.unit,
            sp.purchasePrice,
            sp.salePrice,
            sp.description,        
            sp.FK_idCategory,
            s.address AS storeAddress,
            (ssp.stockQty < ssp.warningQty) AS isLowStock -- Sửa: Sử dụng biểu thức boolean MySQL
        ");

        $builder->orderBy('isLowStock', 'DESC');
        $builder->orderBy('sp.sparePartName', 'ASC'); 
        
        $builder->limit($limit, $offset);
        $data = $builder->get()->getResultArray();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    public function updateSparePartDetails(string $partId, string $storeId, array $data): bool
    {
        $this->db->transStart();

        if (isset($data['salePrice'], $data['purchasePrice']) && $data['salePrice'] < $data['purchasePrice']) {
            throw new Exception('Giá bán không được nhỏ hơn giá mua.');
        }

        $partData = array_filter([
            'sparePartName' => $data['sparePartName'] ?? null,
            'FK_idCategory' => $data['FK_idCategory'] ?? null,
            'unit'          => $data['unit'] ?? null,
            'purchasePrice' => $data['purchasePrice'] ?? null,
            'salePrice'     => $data['salePrice'] ?? null,
            'description'   => $data['description'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($partData)) {
            $this->update($partId, $partData);
        }

        $storePartData = array_filter([
            'location'   => $data['location'] ?? null,
            'warningQty' => $data['warningQty'] ?? null,
            'stockQty'   => $data['stockQty'] ?? null,
        ], fn($v) => $v !== null);

        if (!empty($storePartData)) {
            $this->db->table('store_sparepart')
                ->where('FK_idSparePart', $partId)
                ->where('FK_idStore', $storeId)
                ->update($storePartData);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new Exception('Cập nhật thất bại do lỗi transaction.');
        }

        return true;
    }
}
